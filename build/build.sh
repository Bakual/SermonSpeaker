#!/bin/sh
rm -rf packages && mkdir packages
# Component
cd ../com_sermonspeaker/
zip -r ../build/packages/com_sermonspeaker.zip *
# Modules
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
# Plugins
cd ../plg_content_sermonspeaker/
zip -r ../build/packages/plg_content_sermonspeaker.zip *
cd ../plg_editors_xtd_sermonspeaker/
zip -r ../build/packages/plg_editors_xtd_sermonspeaker.zip *
cd ../plg_finder_sermonspeaker/
zip -r ../build/packages/plg_finder_sermonspeaker.zip *
cd ../plg_sermonspeaker_search/
zip -r ../build/packages/plg_sermonspeaker_search.zip *
# Package
cd ../build/packages/
cp ../../pkg_sermonspeaker.xml pkg_sermonspeaker.xml
zip pkg_sermonspeaker.zip *
rm pkg_sermonspeaker.xml
