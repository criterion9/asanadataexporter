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

Provide your personal access token (PAT) using one of these options:
<ol>
<li>Save your personal access token to the first line in a new file here:  
config/.asana_token .  </li>
<li>Use the optional parameter when running the commandline:  
--token <PAT>  </li>
<li>Provide your PAT when prompted when executing the commandline</li>
</ol>
See [Personal access token](https://developers.asana.com/docs/personal-access-token)
for more details on how to generate your token.
</li>
<li>Run the console application

````bash
bin/console asanadataexporter:export
````
Arguments & Parameters
<dl>
<dt><token></dt>
<dd>Your Asana Personal Access Token (PAT)</dd>
<dt><output></dt>
<dd>Folder to output the exported data</dd>
<dt>--outputsubfolder</dt>
<dd>Working directory to output exported data. Defaults to the current timestamp.
Use this option to continue an export that failed to complete.
<dt>--workspace</dt>
<dd>The workspace to export, automatically selected if you only have access to 
1 workspace.</dd>
<dt>--team</dt>
<dd>The team to export</dd>
<dt>--project</dt>
<dd>The project to export</dd>
<dt>--include_subtasks</dt>
<dd>Whether to export subtasks, default is true</dd>
<dt>--include_attachments</dt>
<dd>Whether to export attachments, default is true</dd>
<dt>--include_projectstatus</dt>
<dd>Whether to export project statuses, default is true</dd>
<dt>--compress_output</dt>
<dd>Whether to compress the output, default is true</dd>
<dt>--keep_raw_output</dt>
<dd>Whether to keep the raw output or remove all working files after compression, 
default is false</dd>
<dt>--speed</dt>
<dd>The speed (one of "slow", "normal", or "fast") to run the export requests, 
the default is "normal"</dd>
</dl>
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
a stand alone library including a command line option.
 