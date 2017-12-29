<?php


/* Define some directories*/
define('COMPOSER_PATH', base_path());

// session_save_path(COMPOSER_PATH);
// session_start();

/*These are already autoload by laravel*/
// //require_once COMPOSER_PATH.'/vendor/autoload.php';
// require_once COMPOSER_PATH.'/vendor/google/apiclient/src/Google/autoload.php';

define('APPLICATION_NAME', 'Media Manager');
define('CREDENTIALS_PATH', COMPOSER_PATH.'/app/.credentials/drive-api-quickstart.json');
define('CLIENT_SECRET_PATH', COMPOSER_PATH.'/app/.credentials/client_secret.json');
define('SCOPES', implode(' ', array(
  Google_Service_Drive::DRIVE)//_METADATA_READONLY)
));

class EasyDriveAPI2
{
    //API client and service object.
    private $client = null;
    private $service = null;

    public static $folder_mime_type = 'application/vnd.google-apps.folder' ;

    
    /**
     * Construct the service object
     */
    public function getService()
    {
        if ($this->service != null)
        {
            return $this->service;
        }
        
        return new Google_Service_Drive($this->client);
    }
    
    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    public function getClient($redirect_url) 
    {

        if ($this->client != null)
        {
            return $this->client;
        }
        
        $client = new Google_Client();
        $client->setApplicationName(APPLICATION_NAME);
        $client->setScopes(SCOPES);
        
        // $client->addScope("https://www.googleapis.com/auth/drive");
        // $client->setAssertionCredentials(new Google_Auth_AssertionCredentials(
        //     SERVICE_ACCOUNT_NAME,
        //     array('https://www.googleapis.com/auth/drive', 'https://www.googleapis.com/auth/drive.apps.readonly'),
        //     KEY)
        // );
        // $client->setAssertionCredentials(new Google_Auth_AssertionCredentials(
        //     EMAIL,
        //     array('https://www.googleapis.com/auth/drive', 'https://www.googleapis.com/auth/drive.apps.readonly'),
        //     KEY)
        // );
        //$client->setScopes("https://www.googleapis.com/auth/drive");//permission
        
        $client->setAuthConfigFile(CLIENT_SECRET_PATH);
        $client->setRedirectUri($redirect_url);
        $client->setAccessType('offline');

        // Load previously authorized credentials from a file.
        
        $credentialsPath = CREDENTIALS_PATH;
        if (file_exists($credentialsPath)) 
        {
            $accessToken = file_get_contents($credentialsPath);
        } else 
        {
            // Request authorization from the user.

            //TODO: get an alternate way to get authorization code from user. The STDIN doesn't work for browser!
            echo 'authorization failed';
            die();

            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->authenticate($authCode);

            // Store the credentials to disk.
            if(!file_exists(dirname($credentialsPath))) 
            {
              mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, $accessToken);
            printf("Credentials saved to %s\n", $credentialsPath);
        }
        $client->setAccessToken($accessToken);
        //echo $accessToken;echo '<br/>';
        

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->refreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, $client->getAccessToken());
        }
        return $client;
    }

    public function setRedirectUrl($redirect_url)
    {
      $this->client->setRedirectUri($redirect_url);
    }
    
    /**
     * Get the API client and construct the service object.
     */
    public function __construct($redirect_url)
    {
        $this->client = $this->getClient($redirect_url);
        $this->service = new Google_Service_Drive($this->client);

                       
        /* The following authorization is not required if we are using authorization from a file*/

        // if (isset($_REQUEST['logout'])) {
        //     unset($_SESSION['upload_token']);
        // }

        // if (isset($_GET['code'])) {
        //     $this->client->authenticate($_GET['code']);
        //     $_SESSION['upload_token'] = $this->client->getAccessToken();
        //     $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        //     header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
        //     exit();
        // }

        // if (isset($_SESSION['upload_token']) && $_SESSION['upload_token']) {
        //     $this->client->setAccessToken($_SESSION['upload_token']);
        //     if ($this->client->isAccessTokenExpired()) {
        //         unset($_SESSION['upload_token']);
        //     }
        // } else {
          
        //   $auth_url = $this->client->createAuthUrl();
        //   // echo "<a class='login' href='" . $auth_url . "'>Connect Me!</a>";
        //   // die();

        //   header('location:'.$auth_url);
        //   exit();
        //   //echo 'Authentication failed';die();
        // }

    }

    /*
     * Checks if a given file id is a folder
     */
    public function isFolder($id)
    {
      $file = $this->service->files->get($id);
      return ($file->getMimeType() == 'application/vnd.google-apps.folder');
    }

    /**
     * find if a file is trashed
     */
    public function isTrashed($file_id)
    {
      $file = $this->service->files->get($file_id);
      return $file->getLabels()->getTrashed();
    }

    /**
     * Print a file's parents.
     *
     * @param Google_Service_Drive $service Drive API service instance.
     * @param String $fileId ID of the file to print parents for.
     */
    function getParents($fileId) {
      $ids = [];
      try {
        $parents = $this->service->parents->listParents($fileId);
        foreach ($parents->getItems() as $parent) {
          if ($this->isTrashed($parent->getId()))
          {
            continue;
          }
          $ids = $parent->getId();
        }
      } catch (Exception $e) {
        print "An error occurred: " . $e->getMessage();
      }

      return $ids;
    }
    /**
     * Gets children (their Ids) of a parent
     * Get file Ids of a file $file_name if specified
     */
    public function getChildren($parent_id,$file_name='')
    {
        $pageToken = NULL;
        $ids = array();
        do {
          try {
            $parameters = array();

            if($file_name != '')
            {
              $parameters['q'] = "title = '$file_name'";
            }
            
            if ($pageToken) {
              $parameters['pageToken'] = $pageToken;
            }

            $children = $this->service->children->listChildren($parent_id, $parameters);

            
            
            foreach ($children->getItems() as $child) {
              
              $id = $child->getId();
              
              $file = $this->service->files->get($id);
              
              if($file->getLabels()->getTrashed())
              {
                //I am not interested in trashed files
                continue;
              }

              $ids[] = $id;
              
            }

            $pageToken = $children->getNextPageToken();
          } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
            $pageToken = NULL;
          }
        } while ($pageToken);
        return $ids;
    }

     /**
     * Insert new file.
     *
     * @param Google_Service_Drive $service Drive API service instance.
     * @param string $title Title of the file to insert, including the extension.
     * @param string $description Description of the file to insert.
     * @param string $parentId Parent folder's ID.
     * @param string $mimeType MIME type of the file to insert.
     * @param string $filename Filename of the file to insert.
     * @return Google_Service_Drive_DriveFile The file that was inserted. NULL is
     *     returned if an API error occurred.
     */
    function insertFile($title, $description, $parentId, $mimeType, $filename) 
    {
      $file = new Google_Service_Drive_DriveFile();
      $file->setTitle($title);
      $file->setDescription($description);
      $file->setMimeType($mimeType);

      // Set the parent folder.
      if ($parentId != null) 
      {
        $parent = new Google_Service_Drive_ParentReference();
        $parent->setId($parentId);
        $file->setParents(array($parent));
      }

      try 
      {
        $data = file_get_contents($filename);

        $createdFile = $this->service->files->insert($file, array(
          'data' => $data,
          'mimeType' => $mimeType,
          'uploadType' => 'multipart'
        ));

        // Uncomment the following line to print the File ID
        // print 'File ID: %s' % $createdFile->getId();
        $newPermission = new Google_Service_Drive_Permission();
        $newPermission->setType('anyone');
        $newPermission->setRole('reader');
        $this->service->permissions->insert($createdFile->getId(), $newPermission);

        return $createdFile;
      } 
      catch (Exception $e) 
      {
        print "An error occurred: " . $e->getMessage();
        die();
      }
    }

    public function createFolder($folder_name,$parentId,$description = 'Folder created by google API')
    {
        $file = new Google_Service_Drive_DriveFile();

        //Setup the Folder to Create
        $file->setTitle($folder_name);
        $file->setDescription($description);
        $file->setMimeType('application/vnd.google-apps.folder');
        

        // Set the parent folder.
        if ($parentId != null) 
        {
            $parent = new Google_Service_Drive_ParentReference();
            $parent->setId($parentId);
            $file->setParents(array($parent));
        }

        try 
        {
            //create the ProjectFolder in the Parent
            $createdFile = $this->service->files->insert($file, array(
                'uploadType' => 'multipart'
            ));

            // Uncomment the following line to print the File ID
            // print 'File ID: %s' % $createdFile->getId();

            return $createdFile;
        } 
        catch (Exception $e) 
        {
            print "An error occurred: " . $e->getMessage();
        }
    }


    /**
     * Move a file to the trash.
     *
     * @param Google_Service_Drive $service Drive API service instance.
     * @param String $fileId ID of the file to trash.
     * @return Google_Servie_Drive_DriveFile The updated file. NULL is returned if
     *     an API error occurred.
     */
    public function trashFile($fileId) 
    {
      try {
        return $this->service->files->trash($fileId);
      } catch (Exception $e) {
        print "An error occurred: " . $e->getMessage();
      }
      return NULL;
    }

    /**
     * Download a file's content.
     *
     * @param Google_Service_Drive $service Drive API service instance.
     * @param File $file Drive File instance.
     * @return String The file's content if successful, null otherwise.
     */
    function downloadFile($file_id) 
    {
      $file = $this->service->files->get($file_id);
      $downloadUrl = $file->getDownloadUrl();
      if ($downloadUrl) {
        $request = new Google_Http_Request($downloadUrl, 'GET', null, null);
        $httpRequest = $this->service->getClient()->getAuth()->authenticatedRequest($request);
        if ($httpRequest->getResponseHttpCode() == 200) {
          return $httpRequest->getResponseBody();
        } else {
          // An error occurred.
          return null;
        }
      } else {
        // The file doesn't have any content stored on Drive.
        return null;
      }
    }

    /**
     * Return download link of a file
     */
    function getDownloadLink($file_id)
    {
      $file = $this->service->files->get($file_id);
      return $file->getWebContentLink();
    }

    /**
     * Rename a file.
     *
     * @param apiDriveService $service Drive API service instance.
     * @param string $fileId ID of the file to rename.
     * @param string $newTitle New title for the file.
     * @return DriveFile The updated file. NULL is returned if an API error occurred.
     */
    function renameFile($fileId, $newTitle) {
      try {
        $file = new Google_Service_Drive_DriveFile();
        $file->setTitle($newTitle);

        $updatedFile = $this->service->files->patch($fileId, $file, array(
          'fields' => 'title'
        ));

        return $updatedFile;
      } catch (Exception $e) {
        print "An error occurred: " . $e->getMessage();
      }
    }
    
    /**
     * Move a file.
     *
     * @param Google_Service_Drive_DriveFile $service Drive API service instance.
     * @param string $fileId ID of the file to move.
     * @param string $newParentId Id of the folder to move to.
     * @return Google_Service_Drive_DriveFile The updated file. NULL is returned if an API error occurred.
     */
    function moveFile($fileId, $newParentId) {
      try {
        $file = new Google_Service_Drive_DriveFile();

        $parent = new Google_Service_Drive_ParentReference();
        $parent->setId($newParentId);

        $file->setParents(array($parent));

        $updatedFile = $this->service->files->patch($fileId, $file);

        return $updatedFile;
      } catch (Exception $e) {
        print "An error occurred: " . $e->getMessage();
      }
  }

   



}