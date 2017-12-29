<div class="row">
  <div class="col-sm-12">
    <table id="pageList" class="table table-bordered table-striped">
      <thead>
        <th>SN</th>
        <th>File name</th>
        <th>Subject</th>
        <th>Uploaded by</th>
        <th>Upload date</th>
        <th>Downloaded</th>
        <th>Action</th>
      </thead>
      <tbody>
        @define $i=1
        @foreach($files as $file)
          <tr>
            <td>{{$i++}}</td>
            <td>{{$file->filename}}</td>
            <td>{{$file->subject_name}}</td>
            <td>{{$file->created_by}}</td>
            <td>{{substr($file->created_at, 0, 10)}}</td>
            <td>{{$file->no_of_downloads}}</td>
            <td>
              <a href="{{URL::route('download-manager-backend-file-download',[$file->download_id,$file->google_file_id])}}" class="btn btn-info btn-flat" data-toggle="tooltip" title="Download" >
                <i class="fa fa-fw fa-download"></i>
              </a>
                          
            </td>

          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>