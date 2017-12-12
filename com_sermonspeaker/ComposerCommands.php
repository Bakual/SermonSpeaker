<?php
/**
 * Created by PhpStorm.
 * User: Thomas
 * Date: 29.04.2017
 * Time: 23:17
 */

namespace Bakual\Sermonspeaker;

class ComposerCommands
{
	public static function cleanup()
	{
		self::deleteDirectory('james-heinrich/getid3/demos');
		self::deleteDirectory('james-heinrich/getid3/helperapps');
		self::deleteDirectory('james-heinrich/getid3/licenses');
		self::deleteDirectory('aws/aws-sdk-php/.changes');
		self::deleteDirectory('mtdowling/jmespath.php/tests/compliance/perf');
		self::deleteDirectory('mtdowling/jmespath.php/tests/compliance');
		self::deleteDirectory('mtdowling/jmespath.php/tests');
		self::deleteDirectory('mtdowling/jmespath.php/bin');
		self::deleteDirectory('bin');
		$files = array(
			'james-heinrich\getid3\getid3\extension.cache.dbm.php',
			'james-heinrich\getid3\getid3\extension.cache.mysql.php',
			'james-heinrich\getid3\getid3\extension.cache.mysqli.php',
			'james-heinrich\getid3\getid3\extension.cache.sqlite3.php',
			'james-heinrich\getid3\getid3\module.archive.gzip.php',
			'james-heinrich\getid3\getid3\module.archive.rar.php',
			'james-heinrich\getid3\getid3\module.archive.szip.php',
			'james-heinrich\getid3\getid3\module.archive.tar.php',
			'james-heinrich\getid3\getid3\module.archive.zip.php',
			'james-heinrich\getid3\getid3\module.audio.aa.php',
			'james-heinrich\getid3\getid3\module.audio.ac3.php',
			'james-heinrich\getid3\getid3\module.audio.amr.php',
			'james-heinrich\getid3\getid3\module.audio.au.php',
			'james-heinrich\getid3\getid3\module.audio.avr.php',
			'james-heinrich\getid3\getid3\module.audio.bonk.php',
			'james-heinrich\getid3\getid3\module.audio.dsf.php',
			'james-heinrich\getid3\getid3\module.audio.dss.php',
			'james-heinrich\getid3\getid3\module.audio.dts.php',
			'james-heinrich\getid3\getid3\module.audio.flac.php',
			'james-heinrich\getid3\getid3\module.audio.la.php',
			'james-heinrich\getid3\getid3\module.audio.lpac.php',
			'james-heinrich\getid3\getid3\module.audio.midi.php',
			'james-heinrich\getid3\getid3\module.audio.mod.php',
			'james-heinrich\getid3\getid3\module.audio.monkey.php',
			'james-heinrich\getid3\getid3\module.audio.mpc.php',
			'james-heinrich\getid3\getid3\module.audio.ogg.php',
			'james-heinrich\getid3\getid3\module.audio.optimfrog.php',
			'james-heinrich\getid3\getid3\module.audio.rkau.php',
			'james-heinrich\getid3\getid3\module.audio.shorten.php',
			'james-heinrich\getid3\getid3\module.audio.tta.php',
			'james-heinrich\getid3\getid3\module.audio.voc.php',
			'james-heinrich\getid3\getid3\module.audio.vqf.php',
			'james-heinrich\getid3\getid3\module.audio.wavpack.php',
			'james-heinrich\getid3\getid3\module.audio-video.asf.php',
			'james-heinrich\getid3\getid3\module.audio-video.bink.php',
			'james-heinrich\getid3\getid3\module.audio-video.matroska.php',
			'james-heinrich\getid3\getid3\module.audio-video.nsv.php',
			'james-heinrich\getid3\getid3\module.audio-video.real.php',
			'james-heinrich\getid3\getid3\module.audio-video.riff.php',
			'james-heinrich\getid3\getid3\module.audio-video.swf.php',
			'james-heinrich\getid3\getid3\module.audio-video.ts.php',
			'james-heinrich\getid3\getid3\module.graphic.bmp.php',
			'james-heinrich\getid3\getid3\module.graphic.efax.php',
			'james-heinrich\getid3\getid3\module.graphic.gif.php',
			'james-heinrich\getid3\getid3\module.graphic.jpg.php',
			'james-heinrich\getid3\getid3\module.graphic.pcd.php',
			'james-heinrich\getid3\getid3\module.graphic.png.php',
			'james-heinrich\getid3\getid3\module.graphic.svg.php',
			'james-heinrich\getid3\getid3\module.graphic.tiff.php',
			'james-heinrich\getid3\getid3\module.misc.cue.php',
			'james-heinrich\getid3\getid3\module.misc.exe.php',
			'james-heinrich\getid3\getid3\module.misc.iso.php',
			'james-heinrich\getid3\getid3\module.misc.msoffice.php',
			'james-heinrich\getid3\getid3\module.misc.par2.php',
			'james-heinrich\getid3\getid3\module.misc.pdf.php',
			'james-heinrich\getid3\getid3\module.tag.xmp.php',
			'james-heinrich\getid3\getid3\write.vorbiscomment.php',
			'james-heinrich\getid3\getid3\write.real.php',
			'james-heinrich\getid3\getid3\write.metaflac.php',
			'james-heinrich\getid3\getid3\write.lyrics3.php',
			'james-heinrich\getid3\getid3\write.id3v1.php',
			'james-heinrich\getid3\getid3\write.apetag.php',
			'james-heinrich\getid3\.gitattributes',
			'james-heinrich\getid3\.gitignore',
			'james-heinrich\getid3\changelog.txt',
			'james-heinrich\getid3\composer.json',
			'james-heinrich\getid3\dependencies.txt',
			'james-heinrich\getid3\license.txt',
			'james-heinrich\getid3\README.md',
			'james-heinrich\getid3\readme.txt',
			'james-heinrich\getid3\structure.txt',
			'mtdowling/jmespath.php/.gitignore',
			'mtdowling/jmespath.php/.travis.yml',
			'mtdowling/jmespath.php/CHANGELOG.md',
			'mtdowling/jmespath.php/composer.json',
			'mtdowling/jmespath.php/Makefile',
			'mtdowling/jmespath.php/Makefile',
			'mtdowling/jmespath.php/phpunit.xml.dist',
			'mtdowling/jmespath.php/README.rst',
			'psr/http-message/CHANGELOG.md',
			'psr/http-message/composer.json',
			'psr/http-message/README.md',
		);

		foreach ($files as $file)
		{
			$file = __DIR__ . '/admin/vendor/' . $file;

			if (file_exists($file))
			{
				unlink($file);
				echo "File deleted! ({$file})\n";
			}
		}
	}

	private static function deleteDirectory($dir)
	{
		$dir = __DIR__ . '/admin/vendor/' . $dir;

		$dir_handle = is_dir($dir) ? opendir($dir) : false;

		// Falls Verzeichnis nicht geoeffnet werden kann, mit Fehlermeldung terminieren
		if (!$dir_handle)
		{
			return false;
		}

		while ($file = readdir($dir_handle))
		{
			if ($file != "." && $file != "..")
			{
				if (!is_dir($dir . "/" . $file))
				{
					//Datei loeschen
					@unlink($dir . "/" . $file);
				}
				else
				{
					//Falls es sich um ein Verzeichnis handelt, "deleteDirectory" aufrufen
					self::deleteDirectory($dir . '/' . $file);
				}
			}
		}

		closedir($dir_handle);

		//Verzeichnis löschen
		rmdir($dir);

		echo "Directory deleted! ({$dir})\n";

		return true;
	}
}
