<?php

require_once( 'Connections/transcribe.php' );
include( "functions.php" );

?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="boilerplate-home.css">
    <link rel="stylesheet" href="index.css">
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">
    <script src="jquery/jquery-1.11.1.min.js"></script>
    <script src="functions.js"></script>

    <style>
        #body2 {
            background: #fff;
            font: normal 100%;
            position: relative;
            /* height: 100%; */
        }
    </style>
    <title>AIScribe: Automated Audio Transcription</title>
</head>

<body>

    <div id="primaryContainer" class="primaryContainer clearfix">
        <div id="headerBG" class="clearfix">

            <a href="index.php"><img id="logo" src="img-home/logo.png" class="image" /></a>

            <?php if(isset( $_SESSION[ 'MM_Username' ] )) { ?>

            <a href="my-files.php"><img id="login" src="img-home/my-files.png" class="image" /></a>

            <?php } else {?>
            <a href="create-account.php"><img id="create-account" src="img-home/create-account.png" class="image" /></a>

            <a href="login.php"><img id="login" src="img-home/login.png" class="image" /></a>
            <?php } ?>
        </div>

        <div id="header-img-large" class="clearfix">
            <p id="Transcription_Made_Easy">
                Transcription <br/>Made Easy
            </p>
            <p id="Upload_audio_video_files_With_AIScribe_get_searchable_editab">
                With AIScribe, upload audio files and get searchable, editable transcripts in minutes. Like magic.
            </p>
            <?php if(!isset( $_SESSION[ 'MM_Username' ] )) { ?>
            <a href="create-account.php"><img id="get-started" src="img-home/get-started.png" class="image" /></a>
            <?php } ?>
        </div>
        <div id="box" class="clearfix">
            <p id="Transcribe_Your_Audio">
                Transcribe Your Audio
            </p>
            <p id="Powerful_real-time_speech_recognition">
                Powerful real-time speech recognition
            </p>
            <p id="Automatically_transcribe_audio_from_7_languages_in_real-time_an">
                Automatically transcribe audio from 10 languages in real-time and various audio formats.</p>
            <p id="Highly_accurate_speech_detection">
                Highly accurate speech detection
            </p>
            <p id="Recognize_different_speakers_in_your_audio_and_spot_keywords_in">
                Recognize different speakers in your audio and spot keywords in real-time with confidence.</p>
            <p id="Built_to_support_many_use_cases">
                Built to support many use cases
            </p>
            <p id="Transcribe_audio_for_various_use_cases_ranging_from_microphone_">
                Transcribe audio for various use cases ranging from microphone audio to analyzing hours of audio recordings from your call center.</p>
        </div>
        <div id="box1" class="clearfix">
            <p id="Document_Translation_Made_Easy">
                Document Translation Made Easy
            </p>
            <p id="Translate_transcriptions_or_upload_files">
                Translate transcriptions or upload files
            </p>
            <p id="Translate_your_transcribed_audio_or_upload_files_to_communicate">
                Translate your transcribed audio or upload files to communicate with your customers in their own language.</p>
            <p id="Customize_Translation">
                Customize Translation
            </p>
            <p id="Customize_the_translations_based_on_your_unique_terminology_and">
                Customize the translations based on your unique terminology and language.</p>
            <p id="Supported_Languages">
                Supported Languages
            </p>
            <p id="Dynamically_translate_news_patents_or_conversational_document">
                Dynamically translate news, patents, or conversational documents in 20&#x2b; languages&#x21;
            </p>
        </div>
        <p id="Make_Your_Message_Global">
            Make Your Message Global
        </p>
        <img id="usa" src="img-home/usa.png" class="image"/>
        <img id="czech" src="img-home/czech.png" class="image"/>
        <img id="denmark" src="img-home/denmark.png" class="image"/>
        <img id="india" src="img-home/india.png" class="image"/>
        <img id="netherlands" src="img-home/netherlands.png" class="image"/>
        <img id="france" src="img-home/france.png" class="image"/>
        <img id="germany" src="img-home/germany.png" class="image"/>
        <img id="china" src="img-home/china.png" class="image"/>
        <img id="spain" src="img-home/spain.png" class="image"/>
        <img id="sweden" src="img-home/sweden.png" class="image"/>
        <img id="brazil" src="img-home/brazil.png" class="image"/>
        <img id="norway" src="img-home/norway.png" class="image"/>
        <img id="russia" src="img-home/russia.png" class="image"/>
        <img id="italy" src="img-home/italy.png" class="image"/>
        <div id="icons-bg" class="clearfix">
            <p id="Train_Transcription_for_your_Industry">
                Train Transcription for your Industry
            </p>
            <img id="law" src="img-home/law.png" class="image"/>
            <img id="health" src="img-home/health.png" class="image"/>
            <img id="education" src="img-home/education.png" class="image"/>
            <img id="gov" src="img-home/gov.png" class="image"/>
            <p id="AIScribe’s_automated_transcription_serves_many_use_cases_inclu">
                AIScribe’s automated transcription serves many use cases, including industries such as&#x3a; law, healthcare, education, media, marketing, media, research, government, and many others. AIScribe is for everyone.</p>
        </div>
        <div id="box2" class="clearfix">
            <img id="phone" src="img-home/phone3.png" class="image"/>
            <div id="box3" class="clearfix">
                <p id="AIScribe_on_The_Go">
                    AIScribe on The Go
                </p>
                <p id="Record_on_your_iPhone_and_upload_directly_to_AIScribe_Youʼll_g">
                    Record on your iPhone and upload directly to AIScribe. Youʼll get automated transcription in minutes.
                </p>
                <div id="box4" class="clearfix">
                    <img id="app-store" src="img-home/app-store.png" class="image"/>
                    <img id="android" src="img-home/android.png" class="image"/>
                </div>
            </div>
        </div>
        <div id="footer" class="clearfix">
            <img id="logo_footer" src="img-home/logo footer.png" class="image"/>
            <div id="box5" class="clearfix">
            </div>
            <p id="Copyright_2019_AISCRIBE">
                Copyright 2019 AISCRIBE
            </p>
        </div>
    </div>
</body>
</html>