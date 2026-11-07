# Unifoundry.com GNU Unifont Glyphs

Source: https://www.unifoundry.com/unifont/

The TTF files in this folder were compiled from:
https://www.unifoundry.com/pub/unifont/unifont-17.0.05/unifont-17.0.05.tar.gz
via the command: `make BUILDFONT=1` and then converted to TTF via
`fontforge -lang=ff -c 'Open($1); Generate($2);' unifont-17.0.05.otf unifont.ttf`
