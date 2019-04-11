export BUILD_DIR=../fitswarm/public/static/web/js/
cd ~/fitswarm/fitswarm-live
./node_modules/.bin/gulp live:dev

export BUILD_DIR=../fitswarm/public/static/web/js/
cd ~/fitswarm/fitswarm-liveswitch
./node_modules/.bin/gulp live:dev

## test
cat /home/muly/fitswarm/fitswarm/public/static/web/js/fitswarm-live.js | grep SELF
