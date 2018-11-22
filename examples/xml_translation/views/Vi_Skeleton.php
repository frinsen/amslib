<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A sample template using the amslib framework">
    <meta name="author" content="chris thomas, antimatter studios">

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">

	<title>AMSLIB: <?=$api->getValue("title")?></title>

	<link rel="shortcut icon" href="<?=$api->getImage("favicon.ico")?>" />

	<?=Amslib_Resource::getStylesheet()?>
	<?=Amslib_Resource::getJavascript()?>
</head>

<body>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">Template: <?=$api->getValue("title")?></a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="pull-right collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                    <li class="<?=$api->getLanguage("en_GB","active")?>">
                    	<a href="<?=$api->changeLanguage("en_GB")?>">English</a>
                    </li>

                    <li class="<?=$api->getLanguage("es_ES","active")?>">
                    	<a href="<?=$api->changeLanguage("es_ES")?>">Spanish</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <!--[if lt IE 7]>
				<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
				<![endif]-->

				<!-- Add your site or application content here -->
				<?=$content?>
            </div>
        </div>

    </div>
    <!-- /.container -->
</body>