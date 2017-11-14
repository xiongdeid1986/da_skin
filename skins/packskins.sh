#!/bin/sh

cd default
tar cvzf ../default.tar.gz *

cd ../power_user
tar cvzf ../power_user.tar.gz *

cd ../enhanced
tar cvzf ../enhanced.tar.gz *

cd ..

exit 0;
