# Asana Data Exporter
A free (LGPL2.1) data export library for use with Asana.

![Version](https://img.shields.io/github/v/release/criterion9/asanadataexporter)
![Size](https://img.shields.io/github/languages/code-size/criterion9/asanadataexporter)
![License](https://img.shields.io/github/license/criterion9/asanadataexporter)

[Introduction](#introduction)
[Setup](#setup)
[Usage](#usage)
* [Mezzio](#mezzio)
* [Laminas MVC](#laminasmvc)
* [Standalone](#standalone)
* [Webhooks](#webhooks)
  

## <a name="introduction" href="#introduction">Introduction</a>
This package provides an exporter client to implement API calls to receive data 
from Asana.

## <a name="setup" href="#setup">Setup</a>
Install libraries
> composer install

## <a name="usage" href="#usage">Usage</a>
This package is built to support mezzio and laminas as well as be available as 
a stand alone library. 

### <a name="mezzio" href="#mezzio">Mezzio</a>
Add the ConfigProvider class to the config aggregator (typically found in config/config.php)
````php
$aggregator = new ConfigAggregator([
 ...
 \Criterion9\AsanaDataExporter\ConfigProvider::class,
 ...
````
Then use the client in your handlers/middleware as needed for your use cases.

### <a name="laminasmvc" href="#laminasmvc">Laminas MVC</a>
There should be no additional steps beyond adding to your project's composer.json required to begin using the library.

### <a name="standalone" href="#standalone">Standalone</a>
There should be no additional steps beyond adding to your project's composer.json required to begin using the library.
