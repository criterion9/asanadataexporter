# Asana Data Exporter
A free (LGPL2.1) data export library for use with Asana.

![Version](https://img.shields.io/github/v/release/criterion9/asanadataexporter)
![Size](https://img.shields.io/github/languages/code-size/criterion9/asanadataexporter)
![License](https://img.shields.io/github/license/criterion9/asanadataexporter)

[Introduction](#introduction)
[Setup](#setup)
* [Mezzio](#mezzio)
* [Laminas MVC](#laminasmvc)
* [Standalone](#standalone)
[Configuration](#configuration)
* [Settings](#settings)
* [Authentication](#authentication)
[Usage](#usage)

## <a name="introduction" href="#introduction">Introduction</a>
This package provides an exporter client to implement API calls to receive data 
from Asana.

## <a name="setup" href="#setup">Setup</a>

### <a name="oneoff" href="#oneoff">One off</a>
<ol>
<li>Install libraries

````bash
composer install
````
</li>
<li>Setup Authentication

Save your personal access token to the first line in a new file: 
'config/.asana_token'.  
See [Personal access token](https://developers.asana.com/docs/personal-access-token) 
for more details on how to generate your token.
</li>
<li>Run the console applications

````bash
bin/console asanadataexporter:export
````

-- or (if execution will stay under 300 seconds) --

````bash
composer asanadataexport
````
</li>

### <a name="mezzio" href="#mezzio">Mezzio</a>
<ol>
<li>Add the ConfigProvider class to the config aggregator (typically found in config/config.php)

````php
$aggregator = new ConfigAggregator([
 ...
 \Criterion9\AsanaDataExporter\ConfigProvider::class,
 ...
````
</li>
<li>Then use the client in your handlers/middleware as needed for your use cases.</li>
</ol>

### <a name="laminasmvc" href="#laminasmvc">Laminas MVC</a>
There should be no additional steps beyond adding to your project's composer.json required to begin using the library.

### <a name="standalone" href="#standalone">Standalone</a>
There should be no additional steps beyond adding to your project's composer.json required to begin using the library.

## <a name="configuration" href="#configuration">Configuration</a>

### <a name="settings" href="#settings">Settings</a>

### <a name="authentication" href="#authentication">Authentication</a>

## <a name="usage" href="#usage">Usage</a>
This package is built to support mezzio and laminas as well as be available as 
a stand alone library include a command line option.
 