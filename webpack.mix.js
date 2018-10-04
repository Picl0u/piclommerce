let mix = require('laravel-mix');
mix.disableNotifications();
const defaultPath = 'public';
const resourcesAssets = 'resources/assets/piclommerce/';
const srcVendor = resourcesAssets + 'vendors/';

mix.setPublicPath(path.normalize(defaultPath));
mix.setResourceRoot(path.normalize(defaultPath));

//destination path configuration
const dest = defaultPath +  "/";
const destFonts = dest + 'fonts/';
const destCss = dest + 'css/';
const destJs = dest + 'js/';
const destImg = dest + 'images/';

const paths = {
    'fontawesome': srcVendor + 'font-awesome/'
};
mix.copy(paths.fontawesome + 'fonts', destFonts);
mix.copy(resourcesAssets + 'images', destImg, false);

mix.js(resourcesAssets + 'js/app.js', destJs);
mix.js(resourcesAssets + 'js/admin.js', destJs);
mix.sass(resourcesAssets + 'sass/app.scss', destCss).options({
    processCssUrls: false,
    outputStyle: 'compressed'
});
mix.sass(resourcesAssets + 'sass/admin.scss', destCss).options({
    processCssUrls: false,
    outputStyle: 'compressed'
});
