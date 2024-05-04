<pre>
    _                            ____        _        
   / \   ___  __ _ _ __   __ _  |  _ \  __ _| |_ __ _ 
  / _ \ / __|/ _` | '_ \ / _` | | | | |/ _` | __/ _` |
 / ___ \\__ \ (_| | | | | (_| | | |_| | (_| | || (_| |
/_/   \_\___/\__,_|_| |_|\__,_| |____/ \__,_|\__\__,_|
                                                      
 _____                       _            
| ____|_  ___ __   ___  _ __| |_ ___ _ __ 
|  _| \ \/ / '_ \ / _ \| '__| __/ _ \ '__|
| |___ >  <| |_) | (_) | |  | ||  __/ |   
|_____/_/\_\ .__/ \___/|_|   \__\___|_|   
           |_|                            
</pre>
# Asana Data Exporter
A free (LGPL2.1) data export library for use with Asana.

![Version](https://img.shields.io/github/v/release/criterion9/asanadataexporter)
![Size](https://img.shields.io/github/languages/code-size/criterion9/asanadataexporter)
![License](https://img.shields.io/github/license/criterion9/asanadataexporter)

## <a name="toc" href="#toc">Table of Contents</a>

* [Introduction](#introduction)  
* [Setup](#setup)  
  * [Prerequisites](#prereqs)  
  * [One off](#oneoff)  
  * [Mezzio](#mezzio)  
  * [Laminas MVC](#laminasmvc)  
  * [Standalone](#standalone)  
* [Configuration](#configuration)  
  * [Settings](#settings)  
  * [Authentication](#authentication)  
* [Usage](#usage)  
  * [Console](#consoleuse)

## <a name="introduction" href="#introduction">Introduction</a>
This package provides an exporter client to implement API calls to receive data 
from Asana.

## <a name="setup" href="#setup">Setup</a>

### <a name="prereqs" href="#prereqs">Prerequisites</a>
* PHP 8  
  * zip extension  
  * sqlite3 extension  
  * curl extension  
* CURL

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

See [Personal access token (PAT)](https://developers.asana.com/docs/personal-access-token) 
for more details on how to generate your token.
</li>
</ol>

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

Config keys typically used in a config/autoload/*.local.php file to provide application specific configuration:  
* asanadataexporter  
  * output  
    * createifnotexist: whether to attempt to create the output directories  
    * include_subtasks: whether to include subtasks or stop with the top level tasks (cards on a board)  
    * include_attachments: whether to download attachments  
    * include_statusupdates: whether to include project status updates  
    * include_json: whether to output json files  
    * include_csv: whether to output csv files  
    * defaultlocation: the location to use as a working space for local cache and outputs  
    * compress: whether to compress the output  
    * cleanaftercompress: whether to remove temporary working files after export completes  
    * adapter: which export adaptor to use (experimental)  
  * useLocalSession: whether to use a local cache of fetched task data (particularly 
helpful when attempting to restarting a failed export without fetching all content that had previously been retrieved)  
  * token: your [PAT](https://developers.asana.com/docs/personal-access-token)  

### <a name="authentication" href="#authentication">Authentication</a>


See [Personal access token (PAT)](https://developers.asana.com/docs/personal-access-token)
for more details on how to generate your token.

## <a name="usage" href="#usage">Usage</a>
This package is built to support mezzio and laminas as well as be available as 
a stand alone library including a command line option.

### <a name="consoleuse" href="#consoleuse">Console</a>
Run the console application
````bash
bin/console asanadataexporter:export
````
Arguments & Parameters
<dl>
<dt>&lt;token&gt;</dt>
<dd>Your Asana Personal Access Token (PAT)</dd>
<dt>&lt;output&gt;</dt>
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