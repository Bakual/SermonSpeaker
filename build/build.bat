REM This will generate the zipfiles for SermonSpeaker in /build/packages
REM This needs the zip binaries from Info-Zip installed. An installer can be found http://gnuwin32.sourceforge.net/packages/zip.htm
setlocal
SET PATH=%PATH%;C:\Program Files (x86)\GnuWin32\bin
rmdir /q /s packages
mkdir packages

REM Component
cd ../com_sermonspeaker/
zip -r ../build/packages/com_sermonspeaker.zip *

REM Modules
cd ../mod_latestsermons/
zip -r ../build/packages/mod_latestsermons.zip *

cd ../mod_related_sermons/
zip -r ../build/packages/mod_related_sermons.zip *

cd ../mod_sermonarchive/
zip -r ../build/packages/mod_sermonarchive.zip *

cd ../mod_sermoncast/
zip -r ../build/packages/mod_sermoncast.zip *

cd ../mod_sermonspeaker/
zip -r ../build/packages/mod_sermonspeaker.zip *

cd ../mod_sermonupload/
zip -r ../build/packages/mod_sermonupload.zip *

cd ../mod_sermonspeaker_admin/
zip -r ../build/packages/mod_sermonspeaker_admin.zip *

cd ../mod_sermonspeaker_categories/
zip -r ../build/packages/mod_sermonspeaker_categories.zip *

REM Plugins
cd ../plg_content_sermonspeaker/
zip -r ../build/packages/plg_content_sermonspeaker.zip *

cd ../plg_editors_xtd_sermonspeaker/
zip -r ../build/packages/plg_editors_xtd_sermonspeaker.zip *

cd ../plg_finder_sermonspeaker/
zip -r ../build/packages/plg_finder_sermonspeaker.zip *

cd ../plg_search_sermonspeaker/
zip -r ../build/packages/plg_search_sermonspeaker.zip *

cd ../plg_sermonspeaker_generic/
zip -r ../build/packages/plg_sermonspeaker_generic.zip *

cd ../plg_sermonspeaker_jwplayer7/
zip -r ../build/packages/plg_sermonspeaker_jwplayer7.zip *

cd ../plg_sermonspeaker_mediaelement/
zip -r ../build/packages/plg_sermonspeaker_mediaelement.zip *

cd ../plg_sermonspeaker_vimeo/
zip -r ../build/packages/plg_sermonspeaker_vimeo.zip *

cd ../plg_content_autotweetsermonspeaker/
zip -r ../build/packages/plg_content_autotweetsermonspeaker.zip *

cd ../plg_quickicon_sermonspeaker/
zip -r ../build/packages/plg_quickicon_sermonspeaker.zip *

exit