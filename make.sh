#/bin/bash
PACKAGE_NAME=de.xxschrandxx.wsc.minecraft-linker
PACKAGE_TYPES=(acptemplates files templates)

for i in "${PACKAGE_TYPES[@]}"
do
    rm -rf ${i}.tar
    7z a -ttar -mx=9 ${i}.tar ./${i}/*
done

rm -rf ${PACKAGE_NAME}.tar ${PACKAGE_NAME}.tar.gz
7z a -ttar -mx=9 ${PACKAGE_NAME}.tar ./* -x!acptemplates -x!files -x!templates -x!${PACKAGE_NAME}.tar -x!${PACKAGE_NAME}.tar.gz -x!.git -x!.gitignore -x!make.sh -x!make.bat -x!.github -x!php_cs.dist -x!phpcs.xml -x!Readme.md -x!pictures -x!constants.php
7z a ${PACKAGE_NAME}.tar.gz ${PACKAGE_NAME}.tar
rm -rf ${PACKAGE_NAME}.tar

for i in "${PACKAGE_TYPES[@]}"
do
    rm -rf ${i}.tar
done
