<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit126059dab1c1fc1a43d94a51ba0b6f25
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
        'b067bc7112e384b61c701452d53a14a8' => __DIR__ . '/..' . '/mtdowling/jmespath.php/src/JmesPath.php',
        '8a9dc1de0ca7e01f3e08231539562f61' => __DIR__ . '/..' . '/aws/aws-sdk-php/src/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'J' => 
        array (
            'JmesPath\\' => 9,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
        ),
        'B' => 
        array (
            'Bakual\\Sermonspeaker\\' => 21,
        ),
        'A' => 
        array (
            'Aws\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'JmesPath\\' => 
        array (
            0 => __DIR__ . '/..' . '/mtdowling/jmespath.php/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
        'Bakual\\Sermonspeaker\\' => 
        array (
            0 => __DIR__ . '/../../..' . '/',
        ),
        'Aws\\' => 
        array (
            0 => __DIR__ . '/..' . '/aws/aws-sdk-php/src',
        ),
    );

    public static $classMap = array (
        'AMFReader' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.flv.php',
        'AMFStream' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.flv.php',
        'AVCSequenceParameterSetReader' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.flv.php',
        'AWS\\CRT\\Auth\\AwsCredentials' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/Auth/AwsCredentials.php',
        'AWS\\CRT\\Auth\\CredentialsProvider' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/Auth/CredentialsProvider.php',
        'AWS\\CRT\\Auth\\Signable' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/Auth/Signable.php',
        'AWS\\CRT\\Auth\\SignatureType' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/Auth/SignatureType.php',
        'AWS\\CRT\\Auth\\SignedBodyHeaderType' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/Auth/SignedBodyHeaderType.php',
        'AWS\\CRT\\Auth\\Signing' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/Auth/Signing.php',
        'AWS\\CRT\\Auth\\SigningAlgorithm' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/Auth/SigningAlgorithm.php',
        'AWS\\CRT\\Auth\\SigningConfigAWS' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/Auth/SigningConfigAWS.php',
        'AWS\\CRT\\Auth\\SigningResult' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/Auth/SigningResult.php',
        'AWS\\CRT\\Auth\\StaticCredentialsProvider' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/Auth/StaticCredentialsProvider.php',
        'AWS\\CRT\\CRT' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/CRT.php',
        'AWS\\CRT\\HTTP\\Headers' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/HTTP/Headers.php',
        'AWS\\CRT\\HTTP\\Message' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/HTTP/Message.php',
        'AWS\\CRT\\HTTP\\Request' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/HTTP/Request.php',
        'AWS\\CRT\\HTTP\\Response' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/HTTP/Response.php',
        'AWS\\CRT\\IO\\EventLoopGroup' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/IO/EventLoopGroup.php',
        'AWS\\CRT\\IO\\InputStream' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/IO/InputStream.php',
        'AWS\\CRT\\Internal\\Encoding' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/Internal/Encoding.php',
        'AWS\\CRT\\Internal\\Extension' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/Internal/Extension.php',
        'AWS\\CRT\\Log' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/Log.php',
        'AWS\\CRT\\NativeResource' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/NativeResource.php',
        'AWS\\CRT\\OptionValue' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/Options.php',
        'AWS\\CRT\\Options' => __DIR__ . '/..' . '/aws/aws-crt-php/src/AWS/CRT/Options.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'getID3' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/getid3.php',
        'getid3_7zip' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.archive.7zip.php',
        'getid3_aac' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.aac.php',
        'getid3_apetag' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.tag.apetag.php',
        'getid3_dsdiff' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.dsdiff.php',
        'getid3_exception' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/getid3.php',
        'getid3_flv' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.flv.php',
        'getid3_handler' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/getid3.php',
        'getid3_hpk' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.archive.hpk.php',
        'getid3_id3v1' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.tag.id3v1.php',
        'getid3_id3v2' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.tag.id3v2.php',
        'getid3_ivf' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.ivf.php',
        'getid3_lib' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/getid3.lib.php',
        'getid3_lyrics3' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.tag.lyrics3.php',
        'getid3_mp3' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.mp3.php',
        'getid3_mpeg' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.mpeg.php',
        'getid3_quicktime' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.quicktime.php',
        'getid3_tag_nikon_nctg' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.tag.nikon-nctg.php',
        'getid3_tak' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.tak.php',
        'getid3_torrent' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.misc.torrent.php',
        'getid3_write_id3v2' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/write.id3v2.php',
        'getid3_writetags' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/write.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit126059dab1c1fc1a43d94a51ba0b6f25::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit126059dab1c1fc1a43d94a51ba0b6f25::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit126059dab1c1fc1a43d94a51ba0b6f25::$classMap;

        }, null, ClassLoader::class);
    }
}
