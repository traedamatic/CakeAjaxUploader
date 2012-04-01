#  CakeAjaxUploader Plugin for CakePHP a (AjaxMultiUpload) - Fork 

A full-blown AJAX file uploader plugin for CakePHP 2.1.
Using this, you can add multiple file upload behaviour to any or all
of your models without having to modify the database or schema.

You can click on the Upload File button, or drag-and-drop files into 
it. You can upload multiple files at a time without having to click
on any button, and it shows you a nice progress notification during
uploads.

## How to Use

### Download or checkout

You can either download the ZIP file:

https://github.com/traedamatic/CakeAjaxUploader/zipball/master

or checkout the code

```
git clone git://github.com/traedamatic/CakeAjaxUploader.git
```

### Put it in the Plugin/ directory

Unzip or move the contents of this to "Plugin/CakeAjaxUploader" under
the app root.

### Add to bootstrap.php load

Open Config/bootstrap.php and add this line:

```php
CakePlugin::load(array(
					'CakeAjaxUploader' => array(
								"bootstrap" => true,
								"routes" => true
								)
					)
				  );
```

This will allow the plugin to load all the files that it needs.

### Create file directory

Make sure to create the correct files upload directory if it doesn't
exist already:
<pre>
cd cake-app-root
mkdir webroot/files
chmod -R 777 webroot/files
</pre>

The default upload directory is "files" under /webroot - but this can
be changed (see FAQ below.) 

You don't have to give it a 777 permission - just make sure the web 
server user can write to this directory.

### Routes

A complete listing of all files in your upload path(including delete link) under:

```
http://www.yourserver.com/filelisting 
```
### Add to controller 

Add to Controller/AppController.php for use in all controllers, or 
in just your specific controller where you will use it as below:

```php
var $helpers = array('AjaxMultiUpload.Upload');
```

### Add to views

Add this to your View/Companies/view.ctp:

```php
echo $this->Upload->view("path/to/my/upload/folder");
```

and this to your View/Companies/edit.ctp:

```php
echo $this->Upload->edit("path/to/my/upload/folder");
```

## FAQ

#### Dude! No database/table schema changes?

Nope. :) Just drop this plugin in the right Plugin/ directory and add 
the code to the controller and views. Make sure the "files" directory
under webroot is writable, otherwise uploads will fail.

No tables/database changes are needed since the plugin uses a directory
structure based on the model name and id to save the appropriate files
 for the model.

#### Help! I get file upload or file size error messages!

The default upload file size limit is set to a conservative 2 MB 
to make sure it works on all (including shared) hosting. To change 
this:

* Open up Plugin/AjaxMultipUpload/Config/bootstrap.php and change the
"AMU.filesizeMB" setting to whatever size in MB you like.
* Make sure to also change the upload size setting (
upload_max_filesize and post_max_size) in your PHP settings (
php.ini) and reboot the web server!

#### Change directory 

Are you stuck to the "files" directory under webroot? Nope.

Open up Config/bootstrap.php under the Plugin/AjaxMultiUpload directory 
and change the "AMU.directory" setting. 

The directory will live under the app webroot directory - this is
as per CakePHP conventions.

#### Change directory paths

Coming soon.

## Future Work

* Rewrite the upload valums PHP script

## Thanks

This uses the Ajax Upload script from: http://valums.com/ajax-upload/
and file icons from: http://www.splitbrain.org/projects/file_icons

## Support

If you find this plugin useful, please consider a [donation to Shen
Yun Performing Arts](https://www.shenyunperformingarts.org/support)
to support traditional and historic Chinese culture.

or

http://cakefoundation.org/pages/donations ;)


