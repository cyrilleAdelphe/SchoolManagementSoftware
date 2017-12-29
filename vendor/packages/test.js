$(document).ready(function(){
  
  var roxyFileman = "http://localhost/techroadians/laravel-fresh/public/fileman?integration=ckeditor";

 $(function(){
  CKEDITOR.replace( 'editor1',{filebrowserBrowseUrl:roxyFileman,
                               filebrowserImageBrowseUrl:roxyFileman+'&type=image',
                               removeDialogTabs: 'link:upload;image:upload'});
  CKEDITOR.replace( 'editor2',{filebrowserBrowseUrl:roxyFileman,
                               filebrowserImageBrowseUrl:roxyFileman+'&type=image',
                               removeDialogTabs: 'link:upload;image:upload'});
  CKEDITOR.replace( 'editor3',{filebrowserBrowseUrl:roxyFileman,
                               filebrowserImageBrowseUrl:roxyFileman+'&type=image',
                               removeDialogTabs: 'link:upload;image:upload'});
  CKEDITOR.replace( 'editor4',{filebrowserBrowseUrl:roxyFileman,
                               filebrowserImageBrowseUrl:roxyFileman+'&type=image',
                               removeDialogTabs: 'link:upload;image:upload'});
  
});
})