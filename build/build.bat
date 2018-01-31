REM This will generate the zipfiles for SermonSpeaker in /build/packages
REM This needs the zip binaries from Info-Zip installed. An installer can be found http://gnuwin32.sourceforge.net/packages/zip.htm
setlocal
SET PATH=%PATH%;C:\Program Files (x86)\GnuWin32\bin
rmdir /q /s packages
rmdir /q /s package
mkdir packages
mkdir package

REM Component
cd ../com_sermonspeaker/
zip -r ../build/packages/com_sermonspeaker.zip *
copy ..\build\packages\com_sermonspeaker.zip ..\build\package

REM Modules
cd ../mod_latestsermons/
zip -r ../build/packages/mod_latestsermons.zip *
copy ..\build\packages\mod_latestsermons.zip ..\build\package

cd ../mod_related_sermons/
zip -r ../build/packages/mod_related_sermons.zip *
copy ..\build\packages\mod_related_sermons.zip ..\build\package

cd ../mod_sermonarchive/
zip -r ../build/packages/mod_sermonarchive.zip *
copy ..\build\packages\mod_sermonarchive.zip ..\build\package

cd ../mod_sermoncast/
zip -r ../build/packages/mod_sermoncast.zip *
copy ..\build\packages\mod_sermoncast.zip ..\build\package

cd ../mod_sermonspeaker/
zip -r ../build/packages/mod_sermonspeaker.zip *
copy ..\build\packages\mod_sermonspeaker.zip ..\build\package

cd ../mod_sermonupload/
zip -r ../build/packages/mod_sermonupload.zip *
copy ..\build\packages\mod_sermonupload.zip ..\build\package

cd ../mod_sermonspeaker_admin/
zip -r ../build/packages/mod_sermonspeaker_admin.zip *
copy ..\build\packages\mod_sermonspeaker_admin.zip ..\build\package

REM Plugins
cd ../plg_content_sermonspeaker/
zip -r ../build/packages/plg_content_sermonspeaker.zip *
copy ..\build\packages\plg_content_sermonspeaker.zip ..\build\package

cd ../plg_editors_xtd_sermonspeaker/
zip -r ../build/packages/plg_editors_xtd_sermonspeaker.zip *
copy ..\build\packages\plg_editors_xtd_sermonspeaker.zip ..\build\package

cd ../plg_finder_sermonspeaker/
zip -r ../build/packages/plg_finder_sermonspeaker.zip *
copy ..\build\packages\plg_finder_sermonspeaker.zip ..\build\package

cd ../plg_search_sermonspeaker/
zip -r ../build/packages/plg_search_sermonspeaker.zip *
copy ..\build\packages\plg_search_sermonspeaker.zip ..\build\package

cd ../plg_sermonspeaker_generic/
zip -r ../build/packages/plg_sermonspeaker_generic.zip *

cd ../plg_sermonspeaker_jwplayer5/
zip -r ../build/packages/plg_sermonspeaker_jwplayer5.zip *

cd ../plg_sermonspeaker_jwplayer6/
zip -r ../build/packages/plg_sermonspeaker_jwplayer6.zip *

cd ../plg_sermonspeaker_jwplayer7/
zip -r ../build/packages/plg_sermonspeaker_jwplayer7.zip *
copy ..\build\packages\plg_sermonspeaker_jwplayer7.zip ..\build\package

cd ../plg_sermonspeaker_mediaelement/
zip -r ../build/packages/plg_sermonspeaker_mediaelement.zip *
copy ..\build\packages\plg_sermonspeaker_mediaelement.zip ..\build\package

cd ../plg_sermonspeaker_pixelout/
zip -r ../build/packages/plg_sermonspeaker_pixelout.zip *
copy ..\build\packages\plg_sermonspeaker_pixelout.zip ..\build\package

cd ../plg_sermonspeaker_vimeo/
zip -r ../build/packages/plg_sermonspeaker_vimeo.zip *
copy ..\build\packages\plg_sermonspeaker_vimeo.zip ..\build\package

cd ../plg_content_churchtoolsermonspeaker/
zip -r ../build/packages/plg_content_churchtoolsermonspeaker.zip *

cd ../plg_quickicon_sermonspeaker/
zip -r ../build/packages/plg_quickicon_sermonspeaker.zip *
copy ..\build\packages\plg_quickicon_sermonspeaker.zip ..\build\package

REM Package
cd ../build/package/
copy ..\..\pkg_sermonspeaker.xml
zip pkg_sermonspeaker.zip *
del pkg_sermonspeaker.xml
copy pkg_sermonspeaker.zip ..\packages
cd ..
rmdir /q /s package
exit