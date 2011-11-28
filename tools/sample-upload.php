<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>S3 Form Upload</title>
</head>
<body>
    <form method="post" action="http://hockdev.local/services/uploads" enctype="multipart/form-data">
	<input type="hidden" name="bucket" value="accountpics"/>
	<input type="hidden" name="filename" value="hock.png"/>
	<input type="file" name="file" /><input type="submit" value="Upload" />
    </form>
</body>
</html>