<?php
require_once( 'Connections/transcribe.php' );
include( "auth.php" );
include( "includes/appstatus.php" );
include( "functions.php" );
include( "includes/nav-query.php" );

?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="boilerplate.css">
    <link rel="stylesheet" href="my-files.css">
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">
    <script src="jquery/jquery-1.11.1.min.js"></script>
    <script src="functions.js"></script>
    <script src="side-nav.js"></script>
</head>

<body>

    <div id="primaryContainer" class="primaryContainer clearfix">
        <div id="headerBG" class="clearfix">
            <span style="font-size:30px;cursor:pointer"><img id="navIcon" name="navIcon" src="img/Hamburger_icon.png" class="image"/></span>

            <a href="index.php"><img id="logo" src="img/logo.png" class="image"/></a>
        </div>
        <?php include("includes/nav.php");?>
        <div id="titleDiv" class="clearfix">
            <div id="headerTxtBG" class="clearfix">
                <p id="headerLbl">Corpora Details</p>
            </div>
        </div>
        <div id="contentBG" class="clearfix">
            <p>&nbsp;</p>
            <div id="renderContent">
                <p>The recommended means of populating a custom language model with words is to add one or more corpora to the model. When you add a corpus, AIScribe analyzes the file and automatically adds any new words that it finds to the custom model. </p>
                <p>&nbsp;</p>
                <p>Adding a corpus to a custom model allows the service to extract domain-specific words in context, which helps ensure better transcription results.</p>
                <p>&nbsp;</p>
                <p>A corpus is a plain text file that contains sample sentences from your domain. The following example shows an abbreviated corpus for the healthcare domain. A corpus file is typically much longer.</p>

                <p id="sampleCorpus">&quot;Am I at risk for health problems during travel? Some people are more likely to have health problems when traveling outside the United States. How Is Coronary Microvascular Disease Treated? If you're diagnosed with coronary MVD and also have anemia, you may benefit from treatment for that condition. Anemia is thought to slow the growth of cells needed to repair damaged blood vessels. What causes autoimmune hepatitis? A combination of autoimmunity, environmental triggers, and a genetic predisposition can lead to autoimmune hepatitis. What research is being done for Spinal Cord Injury? The National Institute of Neurological Disorders and Stroke NINDS conducts spinal cord research in its laboratories at the National Institutes of Health NIH. NINDS also supports additional research through grants to major research institutions across the country. Some of the more promising rehabilitation techniques are helping spinal cord injury patients become more mobile. What is Osteogenesis imperfecta OI?&quot;</p>

                <p>The accuracy of transcription can depend largely on how words are defined in a model and how speakers say them. To improve the service's accuracy, use corpora to provide as many examples as possible of how OOV words are used in the domain. Repeating the OOV words in corpora can improve the quality of the custom language model. How you duplicate the words in corpora depends on how you expect users to say them in the audio that is to be recognized. <strong>The more sentences that you add that represent the context in which speakers use words from the domain, the better the service's recognition accuracy.</strong>
                </p>
            </div>
            <p id="docContentDiv">&nbsp;</p>
        </div>
    </div>

    <?php include("includes/side-nav.php");?>
</body>
</html>
<?php
mysql_free_result( $rsFiles );
?>