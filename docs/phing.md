#Phing Build Instructions

From the top level of repo

	cd trunk/tools
	cp env/dev.sample env/dev
	
Edit env/dev to suit your environment

	./build-smap-dev

Phing will put the build in `your-repo/trunk/build/`.