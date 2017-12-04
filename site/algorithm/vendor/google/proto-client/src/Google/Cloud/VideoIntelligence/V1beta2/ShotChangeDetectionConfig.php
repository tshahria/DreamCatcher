<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/cloud/videointelligence/v1beta2/video_intelligence.proto

namespace Google\Cloud\VideoIntelligence\V1beta2;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Config for SHOT_CHANGE_DETECTION.
 *
 * Generated from protobuf message <code>google.cloud.videointelligence.v1beta2.ShotChangeDetectionConfig</code>
 */
class ShotChangeDetectionConfig extends \Google\Protobuf\Internal\Message
{
    /**
     * Model to use for shot change detection.
     * Supported values: "builtin/stable" (the default if unset) and
     * "builtin/latest".
     *
     * Generated from protobuf field <code>string model = 1;</code>
     */
    private $model = '';

    public function __construct() {
        \GPBMetadata\Google\Cloud\Videointelligence\V1Beta2\VideoIntelligence::initOnce();
        parent::__construct();
    }

    /**
     * Model to use for shot change detection.
     * Supported values: "builtin/stable" (the default if unset) and
     * "builtin/latest".
     *
     * Generated from protobuf field <code>string model = 1;</code>
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Model to use for shot change detection.
     * Supported values: "builtin/stable" (the default if unset) and
     * "builtin/latest".
     *
     * Generated from protobuf field <code>string model = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setModel($var)
    {
        GPBUtil::checkString($var, True);
        $this->model = $var;

        return $this;
    }

}

