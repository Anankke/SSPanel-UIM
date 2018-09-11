shake.js
=======================================

A custom 'shake' event JavaScript plugin for mobile web browsers using device accelerometer.

Installation
---------------------------------------

* Download: [zip](https://github.com/alexgibson/shake.js/zipball/master)
* [NPM](https://www.npmjs.org/): `npm install shake.js`
* [Bower](https://github.com/twitter/bower/): `bower install shake.js`
* Git: `git clone https://github.com/alexgibson/shake.js`

Dependencies
---------------------------------------

Your web browser must support the `devicemotion` event for this plugin to work. Shake.js uses built-in feature detection to determine if it can run in your web browser. It will terminate silently on non-supporting browsers.

http://w3c.github.io/deviceorientation/spec-source-orientation.html

Setup
---------------------------------------

For CommonJS using NPM:

```
var Shake = require('shake.js');
```

For AMD module:

```
define(['./shake'], function(Shake) {
    // ...
});
```

In the browser:

```
<script src="shake.js"></script>
```

Next, create a new Shake instance:

```
var myShakeEvent = new Shake({
    threshold: 15, // optional shake strength threshold
    timeout: 1000 // optional, determines the frequency of event generation
});
```

Start listening to device motion:

```
myShakeEvent.start();
```

Register a `shake` event listener on `window` with your callback:

```
window.addEventListener('shake', shakeEventDidOccur, false);

//function to call when shake occurs
function shakeEventDidOccur () {

    //put your own code here etc.
    alert('shake!');
}
```

You can stop listening for shake events like so:

```
window.removeEventListener('shake', shakeEventDidOccur, false);
```

To stop listening to device motion, you can call:

```
myShakeEvent.stop();
```

Supported web browsers/devices
---------------------------------------

- iOS Safari 4.2.1 (and above)
- Android 4.0.3 (default browser)
- Chrome 41+ for Android
- Opera Mobile (Android)
- BlackBerry PlayBook 2.0
- Firefox for Android
- FirefoxOS Devices
