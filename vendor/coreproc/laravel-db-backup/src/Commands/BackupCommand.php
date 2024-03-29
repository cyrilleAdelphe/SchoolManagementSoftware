<?php namespace Coreproc\LaravelDbBackup\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use AWS;
use Config;
use Guzzle\Http;

include_once(base_path().'/app/modules/download-manager/EasyDriveAPI2.php');

class BackupCommand extends BaseCommand
{

    protected $name = 'db:backup';
    protected $description = 'Backup the default database to `app/storage/dumps`';
    protected $filePath;
    protected $fileName;

    public function fire()
    {
        $databaseDriver = Config::get('database.default', false);

	    $databaseOption = $this->input->getOption('database');

        if ( ! empty($databaseOption)) {
            $databaseDriver = $this->input->getOption('database');
        }

        $database = $this->getDatabase($databaseDriver);

        $this->checkDumpFolder();

        if ($this->argument('filename')) {
            // Is it an absolute path?
            if (substr($this->argument('filename'), 0, 1) == '/') {
                $this->filePath = $this->argument('filename');
                $this->fileName = basename($this->filePath);
            } // It's relative path?
            else {
                $this->filePath = getcwd() . '/' . $this->argument('filename');
                $this->fileName = basename($this->filePath) . '_' . time();
            }
        } else {
            if ( ! empty($databaseOption)) {
                $this->fileName = $this->input->getOption('database') . '_' . time() . '.' . $database->getFileExtension();
            } else {
                $this->fileName = Config::get('database.connections.' . $databaseDriver .'.database') . '_' . time() . '.' . $database->getFileExtension();
            }
            $this->filePath = rtrim($this->getDumpsPath(), '/') . '/' . $this->fileName;
        }

        

        //storing in drive
        $status = $database->dump($this->filePath);
        
        

        if ($status === true) {

            // create zip archive
            //if ($this->option('archive')) {
                
                $zip = new \ZipArchive();
                $zipFileName = $this->input->getOption('database') . '_' . time() . '.zip';
                $zipFilePath = dirname($this->filePath) . '/' . $zipFileName;

                if ($zip->open($zipFilePath, \ZipArchive::CREATE) === true) {
                    $zip->addFile($this->filePath, $this->fileName);
                    $zip->close();

                    // delete .sql files
                    unlink($this->filePath);

                   // change filename and filepath to zip
                    $this->filePath = $zipFilePath;
                    $this->fileName = $zipFileName;

                    $prefix = Config::get('app.url');
                    $prefix = str_replace(["/", ':', '.'], '_', $prefix);
                                


		    $google_file_id = \DownloadManager::where('filename', 'root')->pluck('google_file_id');
                    $drive = new \EasyDriveAPI2(\Config::get('app.url'));
                    $drive->insertFile($prefix.$this->fileName, 'sql backup of '.date('Y-m-d'), $google_file_id, 'application/octet-stream', $this->filePath);
                }
            //}

            // display success message
            if ($this->argument('filename')) {
                $this->line(sprintf($this->colors->getColoredString("\n" . 'Database backup was successful. Saved to %s' . "\n", 'green'), $this->filePath));
            } else {
                $this->line(sprintf($this->colors->getColoredString("\n" . 'Database backup was successful. %s was saved in the dumps folder.' . "\n", 'green'), $this->fileName));
            }

            // upload to s3
            if ($this->option('upload-s3')) {
                $this->uploadS3();
                $this->line($this->colors->getColoredString("\n" . 'Upload complete.' . "\n", 'green'));
                if ($this->option('data-retention-s3')) {
                    $this->dataRetentionS3();
                }

                // remove local archive if desired
                if ($this->option('s3-only')) {
                    unlink($this->filePath);
                }
            }

            $databaseConnectionConfig = Config::get('database.connections.' . $this->input->getOption('database'));
            if ( ! empty($databaseConnectionConfig['slackToken']) && ! empty($databaseConnectionConfig['slackSubDomain'])) {
	            $disableSlackOption = $this->option('disable-slack');
                $disableSlack = ! empty($disableSlackOption);
                if ( ! $this->option('disable-slack')) $this->notifySlack($databaseConnectionConfig);
            }

        } else {
            // todo
            $this->line(sprintf($this->colors->getColoredString("\n" . 'Database backup failed. %s' . "\n", 'red'), $status));
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('filename', InputArgument::OPTIONAL, 'Filename or -path for the dump.'),
        );
    }

    protected function getOptions()
    {
        return array(
            array('database', null, InputOption::VALUE_REQUIRED, 'The database connection to backup'),
            array('upload-s3', 'u', InputOption::VALUE_OPTIONAL, 'Upload the dump to your S3 bucket'),
            array('path-s3', null, InputOption::VALUE_OPTIONAL, 'The folder in which to save the backup'),
            array('data-retention-s3', null, InputOption::VALUE_OPTIONAL, 'Number of days to retain backups'),
            array('disable-slack', null, InputOption::VALUE_NONE, 'Number of days to retain backups'),
            array('archive', null, InputOption::VALUE_OPTIONAL, 'Create zip archive'),
            array('s3-only', null, InputOption::VALUE_OPTIONAL, 'Delete local archive after S3 upload'),
        );
    }

    protected function checkDumpFolder()
    {
        $dumpsPath = $this->getDumpsPath();

        if ( ! is_dir($dumpsPath)) {
            mkdir($dumpsPath);
        }
    }

    protected function uploadS3()
    {
        $bucket = $this->option('upload-s3');
        $s3 = AWS::get('s3');

        $s3->putObject(array(
            'Bucket'     => $bucket,
            'Key'        => $this->getS3DumpsPath() . '/' . $this->fileName,
            'SourceFile' => $this->filePath,
        ));
    }

    protected function getS3DumpsPath()
    {
        if ($this->input->getOption('path-s3')) {
            $path = $this->input->getOption('path-s3');
        } else {
            $path = Config::get('laravel-db-backup::s3.path', 'databases');
        }

        return $path;
    }

    private function dataRetentionS3()
    {
        if ( ! $this->option('data-retention-s3')) {
            return;
        }

        $dataRetention = (int) $this->input->getOption('data-retention-s3');

        if ($dataRetention <= 0) {
            $this->error("Data retention should be a number");
            return;
        }

        $bucket = $this->option('upload-s3');
        $s3 = AWS::get('s3');

        $list = $s3->listObjects(array(
            'Bucket' => $bucket,
            'Marker' => $this->getS3DumpsPath(),
        ));

        $timestampForRetention = strtotime('-' . $dataRetention . ' days');
        $this->info('Retaining data where date is greater than ' . date('Y-m-d', $timestampForRetention));

        $contents = $list['Contents'];

        $deleteCount = 0;
        foreach ($contents as $fileArray) {
            $filePathArray = explode('/', $fileArray['Key']);
            $filename = $filePathArray[count($filePathArray) - 1];

            $filenameExplode = explode('_', $filename);

            $fileTimestamp = explode('.', $filenameExplode[count($filenameExplode) - 1])[0];

            if ($timestampForRetention > $fileTimestamp) {
                $this->info("The following file is beyond data retention and was deleted: {$fileArray['Key']}");
                // delete
                $s3->deleteObject(array(
                    'Bucket' => $bucket,
                    'Key'    => $fileArray['Key']
                ));
                $deleteCount++;
            }
        }

        if ($deleteCount > 0) {
            $this->info($deleteCount . ' file(s) were deleted.');
        }

        $this->info("");
    }

    private function notifySlack($databaseConfig)
    {
        $this->info('Sending slack notification..');
        $data['text'] = "A backup of the {$databaseConfig['database']} at {$databaseConfig['host']} has been created.";
        $data['username'] = "Database Backup";
        $data['icon_url'] = "https://s3-ap-northeast-1.amazonaws.com/coreproc/images/icon_database.png";

        $content = json_encode($data);

        $command = "curl -X POST --data-urlencode 'payload={$content}' 'https://{$databaseConfig['slackSubDomain']}.slack.com/services/hooks/incoming-webhook?token={$databaseConfig['slackToken']}'";

        shell_exec($command);
        $this->info('Slack notification sent!');
    }

}
