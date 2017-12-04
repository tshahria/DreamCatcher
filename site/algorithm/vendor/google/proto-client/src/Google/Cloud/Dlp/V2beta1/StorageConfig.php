<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/privacy/dlp/v2beta1/storage.proto

namespace Google\Cloud\Dlp\V2beta1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Shared message indicating Cloud storage type.
 *
 * Generated from protobuf message <code>google.privacy.dlp.v2beta1.StorageConfig</code>
 */
class StorageConfig extends \Google\Protobuf\Internal\Message
{
    protected $type;

    public function __construct() {
        \GPBMetadata\Google\Privacy\Dlp\V2Beta1\Storage::initOnce();
        parent::__construct();
    }

    /**
     * Google Cloud Datastore options specification.
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2beta1.DatastoreOptions datastore_options = 2;</code>
     * @return \Google\Cloud\Dlp\V2beta1\DatastoreOptions
     */
    public function getDatastoreOptions()
    {
        return $this->readOneof(2);
    }

    /**
     * Google Cloud Datastore options specification.
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2beta1.DatastoreOptions datastore_options = 2;</code>
     * @param \Google\Cloud\Dlp\V2beta1\DatastoreOptions $var
     * @return $this
     */
    public function setDatastoreOptions($var)
    {
        GPBUtil::checkMessage($var, \Google\Cloud\Dlp\V2beta1\DatastoreOptions::class);
        $this->writeOneof(2, $var);

        return $this;
    }

    /**
     * Google Cloud Storage options specification.
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2beta1.CloudStorageOptions cloud_storage_options = 3;</code>
     * @return \Google\Cloud\Dlp\V2beta1\CloudStorageOptions
     */
    public function getCloudStorageOptions()
    {
        return $this->readOneof(3);
    }

    /**
     * Google Cloud Storage options specification.
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2beta1.CloudStorageOptions cloud_storage_options = 3;</code>
     * @param \Google\Cloud\Dlp\V2beta1\CloudStorageOptions $var
     * @return $this
     */
    public function setCloudStorageOptions($var)
    {
        GPBUtil::checkMessage($var, \Google\Cloud\Dlp\V2beta1\CloudStorageOptions::class);
        $this->writeOneof(3, $var);

        return $this;
    }

    /**
     * BigQuery options specification.
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2beta1.BigQueryOptions big_query_options = 4;</code>
     * @return \Google\Cloud\Dlp\V2beta1\BigQueryOptions
     */
    public function getBigQueryOptions()
    {
        return $this->readOneof(4);
    }

    /**
     * BigQuery options specification.
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2beta1.BigQueryOptions big_query_options = 4;</code>
     * @param \Google\Cloud\Dlp\V2beta1\BigQueryOptions $var
     * @return $this
     */
    public function setBigQueryOptions($var)
    {
        GPBUtil::checkMessage($var, \Google\Cloud\Dlp\V2beta1\BigQueryOptions::class);
        $this->writeOneof(4, $var);

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->whichOneof("type");
    }

}

