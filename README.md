
# Avif Express

![Avif Express](https://github.com/Pijushgupta/avif-express/blob/main/avif-express.png)


## Description
**Avif Express** 
Avif Express is a WordPress plugin that converts images to the modern **AVIF** format for faster page loads and reduced bandwidth.
If AVIF is not supported, images are automatically served in **WebP**, ensuring compatibility across all major browsers.

## System/Server Requirement
- **WordPress**: 6.0 or higher  
- **PHP**: 7.3 or higher  
- **ImageMagick or GD**: PHP extension with AVIF support *(required for local AVIF conversion)*  
- **DOM** extension for PHP  

## Installation

**Dev Version**
```sh
git clone https://github.com/Pijushgupta/avif-express.git
```

**WordPress Version**
[Download](https://wordpress.org/plugins/avif-express/)


## Features & Usage

**Conversion Engine** 

![Conversion Engine](https://github.com/Pijushgupta/avif-express/blob/main/readme-screens/conversion-engine.png)

- **Local Conversion**: Recommended if your server has ImageMagick with AVIF support or GD compiled with AVIF support.
- **Cloud Conversion (optional - Currently not operational)**: Use the integrated API service if your server lacks AVIF conversion support.

**Automatically convert image on upload**

![Convert on upload](https://github.com/Pijushgupta/avif-express/blob/main/readme-screens/convert-on-upload.png)

Automatically convert every uploaded image.

**Automatic image Processing in Background**

Schedule Image Processing in Background.

![Background Service](https://github.com/Pijushgupta/avif-express/blob/main/readme-screens/background-image-processing.png)

- **off** : Background Service is disabled.
- **Theme Directory** :  Background Service is enabled for Theme direcory.
- **Upload Directory** : Background Service is enabled for Upload direcory.
- **Theme & Upload Directory** : Background Service is enabled for Theme & Upload direcory.

**Automatically scan Directory**

Automatically scan directories and convert images on a schedule:

- **Hourly**
- **Twice Daily**
- **Daily**
- **Weekly**

**Rendering**

![Rendering](https://github.com/Pijushgupta/avif-express/blob/main/readme-screens/rendering.png)

Choose whether to display converted AVIF images. Lazy loading works only when rendering is enabled.
- **Active**: To show altered avif images. 
- **Inactive**:  To not to show altered images. 

**Lazy Loading**

![lazy loading](https://github.com/Pijushgupta/avif-express/blob/main/readme-screens/lazy-loading.png)

Lazy loading is loading image only when it’s needed(visible), instead of loading everything at once.This helps improve performance, page load speed, and reduces initial resource usage.
- **Inactive**: Keep the lazy loading feature off.
    ```html
    <img src="example.avif" alt="example image" />
    ```
- **Html**: Its uses default lazy loading feature from browser. Target only image(img) tag and iframe tags.
    ```html
    <img src="example.avif" alt="example image" loading="lazy"/>
    ```
- **Java Script**: Removes the src/source from iframe, img, video, and audio elements, replacing them with data-src, and restores them when they become visible in the viewport.
    
    *Initial Stage*:
    ```html
    <img data-src="example.avif" alt="example image" />
    ```
    *Visible Stage*
    ```html
    <img src="example.avif" alt="example image" />
    ```
    - **Root Margin**: Preload Distance — Load media early by extending the detection area outside the viewport. Example: 0px 0px 200px 0px loads items 200px before they appear at the bottom of the screen. Order: top right bottom left.
    
    - **Threshold**: When to Load — Set how much of the media should be visible before it loads. 0% = start loading immediately when seen, 100% = wait until fully visible.

    - **Lazy Background**: Also lazy loads inline background images - experimental.
    
**Fallback Image Type**

![Fallback Image Type](https://github.com/Pijushgupta/avif-express/blob/main/readme-screens/fallback-image-type.png)

If the browser does not support AVIF, the following image type will be served:
- **Original** 
- **WebP**

**Disable on the fly avif conversion**

![disable on the fly conversion](https://github.com/Pijushgupta/avif-express/blob/main/readme-screens/on-the-fly-conversion.png)

When an AVIF version of an image is unavailable, the plugin attempts to generate it on the fly. Since AVIF conversion is slower than WebP and other formats, we recommend keeping this option disabled for better performance.

**Image Quality**

![Image Quality](https://github.com/Pijushgupta/avif-express/blob/main/readme-screens/image-quality.png)

Local Conversion Only
0 - Worst, 100 - Best. High quality will increase the file size.

**Compression Speed**

![Image Compression](https://github.com/Pijushgupta/avif-express/blob/main/readme-screens/image-compression-speed.png)

Local Conversion & GD Only
0 - Super slow, smaller file. 10 - Fast, larger file.

**Upload Directory**

![Convert upload directory](https://github.com/Pijushgupta/avif-express/blob/main/readme-screens/convert-upload-directory.png)

Converts or Delete Images in the upload directory. 

**Theme Directory**

![Convert theme directory](https://github.com/Pijushgupta/avif-express/blob/main/readme-screens/convert-theme-directory.png)

Converts or Delete Images in the theme directory. 
