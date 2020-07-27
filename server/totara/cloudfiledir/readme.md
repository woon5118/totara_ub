# Cloud file content storage

## Use cases

The two primary use cases are on-line/scheduled backups and limiting the size of shared dataroot in web clusters.
It can be also used to share file contents between different environments such as staging and production.

### Cloud backup of file contents

Production server in local hosting facility is using regular filedir in dataroot.
Whenever a new content is uploaded to Totara a copy of file is sent to S3 cloud store
located off-site. Contents are never deleted from the S3 cloud store.


### Sharing of filedir in web cluster via cloud

Site is configured to use a cluster of web servers in order to improve scalability
and performance. Each node is using local filedir as a fast cache and the shared
cloud store is updated after all changes in any cluster node. When adding a new node
local filedir starts empty and is gradually filled during file access.


### Sharing of file contents between different environments

Any cloud file content store can be configured to be read-only
and used for restoring of missing content files only. This eliminates the need to
make a full copy of local filedir when testing upgrades or staging production server changes.


### Offloading of file contents that were not accessed for long time

Some operating system filesystems allow tracking of file last access times.
If there is a full backup in the cloud, then administrator may create a script
that deletes large files that were not accessed recently. In case the missing files
are accessed the contents are automatically downloaded from the cloud store.

## Installation steps

1. Install PHP libraries for cloud providers
2. Add settings to config.php
3. Upgrade or install site if necessary
4. Push existing content files to cloud storage using ```totara/cloudfiledir/cli/store.php --push``` 

## Installation of PHP libraries

To use this plugin external libraries need to be installed via composertotara/cloudfiledir/lib/composer.json.
Alternatively you can use other directory and specify the PHP autoload file in
TOTARA_CLOUDFILEDIR_S3_AUTOLOAD or TOTARA_CLOUDFILEDIR_AZURE_AUTOLOAD constant
defined in config.php file.

It is strongly recommended to update composer dependencies in totara/cloudfiledir/lib/
before each Totara upgrade.

## Cloud store options

All configuration is store directly in config.php files in $CFG->totara_cloudfiledir_stores array,
administration interface can be used to review list of configured stores.

Settings for each store are:

* idnumber - the internal identifier of the store
* provider - either 's3' or 'azure' depending on supported cloud API
* bucket - name of the bucket (or container) in the cloud
* options - provider specific connection options
* add - true means new file contents will be added to store
* delete - true means file contents will be deleted from store when not used locally any more
* restore - true means use the store contents to recover contents that are missing in local filedir
* active  - enable/disable switch for all store operations
* maxinstantuploadsize - maximum size of files that are uploaded immediately to the cloud, bigger files
  are uploaded later via cron task or CLI script


Example:

```
$CFG->totara_cloudfiledir_stores = [
    [
       'idnumber' => 'shared_storage',
        'provider' => 's3',
        'bucket' => 'sharedstorage',
        'options' => [
            'region' => 'us-west-2',
            'profile' => 'default',
        ],
        'add' => true,
        'delete' => true,
        'restore' => true,
        'active' => true,
        'maxinstantuploadsize' => -1, // default, means all new files are uploaded to cloud asap.
    ],
    [
       'idnumber' => 'persistent_backup',
        'provider' => 's3',
        'bucket' => 'persistentbackup',
        'options' => [
            'region' => 'us-west-2',
            'profile' => 'backup',
        ],
        'add' => true,
        'delete' => false,
        'restore' => true,
        'active' => true,
        'maxinstantuploadsize' => 0, // 0 means upload via cron/CLI only.
    ],
];

```

### Amazon S3

Amazon S3 API is supported by vast majority of cloud storage solutions. Totara supports arbitrary
connection options.

Supported connection options are described in Amazon SDK docs,
see: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_configuration.html 

List of other storage solutions that should be compatible with this plugin:

* MinIO - https://docs.min.io/docs/how-to-use-aws-sdk-for-php-with-minio-server.html
* OpenStack Swift - https://docs.openstack.org/mitaka/config-reference/object-storage/configure-s3.html
* Google Storage - https://cloud.google.com/storage/docs/migrating#migration-simple
* OpenIO - https://docs.openio.io/latest/source/integrations/cookbook_nextcloud.html


### Azure Blob Storage

Microsoft Azure Blob Storage is a notable exception because it does not support the S3 API de facto standard.

Connection configuration options are described at
https://docs.microsoft.com/en-us/azure/storage/common/storage-configure-connection-string


### Custom providers

Support for different API can be easily implemented in a small custom PHP class
extending \totara_cloudfiledir\local\provider\base, see
\totara_cloudfiledir\local\provider\s3 and \totara_cloudfiledir\local\provider\azure
classes.


## Cron sync task

If any store has maxinstantuploadsize value set, then system administrator should enabled
push task in scheduled tasks settings. 


## CLI script for maintenance of cloud filedir

Command line script /totara/cloudfiledir/cli/store.php can be used to do the following:

* list all available stores
* fetch list of content files when connecting a new cloud store with existing content
* push local file contents to newly connected empty cloud store or upload missing contents
  to existing cloud store
* reset flags used for skipping of invalid local content
* print list of problems related to cloud store


## CLI script for maintenance of local filedir

There is a new CLI script for maintenance of local filedir directory, see /admin/cli/check_filedir.php.

Expected use cases:

1. Consistency check of all content files in local filedir.
2. Restoring of missing content files from external stores.
3. Deleting of orphaned files from local filedir.
