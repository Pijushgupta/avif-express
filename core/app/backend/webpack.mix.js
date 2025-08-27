const path = require("path");
let mix = require("laravel-mix");
mix.setResourceRoot("../dist");
mix.copyDirectory('./fonts', 'assets/dist/fonts');
mix.js("assets/src/app.js", "assets/dist").vue();
mix.postCss("assets/src/app.css", "assets/dist", [require("tailwindcss")]);
mix.webpackConfig({
    watchOptions: {
        ignored: [
            path.resolve(__dirname, "assets/dist"),
        ],
    },
});