<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 06/04/2018
 * Time: 11:29
 */

namespace App\Service\Helper;
use Symfony\Component\HttpFoundation\Response;


/**
 * A wrapper for holding data to be used for a application/problem+json response
 */
class ApiProblem
{

    private $statusCode;
    private $type;
    private $title;

    private $extraData = array();

    const TYPE_VALIDATION_ERROR = 'validation_error';
    const TYPE_INVALID_REQUEST_BODY_FORMAT = 'invalid_body_format';
    const TYPE_BAD_CREDENTIALS = 'bad_credentials';

    private static $titles = array(
        self::TYPE_VALIDATION_ERROR => 'There was a validation error',
        self::TYPE_INVALID_REQUEST_BODY_FORMAT => 'Invalid JSON format sent',
        self::TYPE_BAD_CREDENTIALS => 'Bad credentials sent'
    );

    public function __construct($statusCode, $type = null)
    {
        $this->statusCode = $statusCode;
        if ($type === null) {
            $type = 'about:blank';
            $title = isset(Response::$statusTexts[$statusCode])
                ? Response::$statusTexts[$statusCode]
                : 'Unknown status code :(';
        } else {
            if (!isset(self::$titles[$type])) {
                throw new \InvalidArgumentException('No title for type '.$type);
            }
            $title = self::$titles[$type];
        }
        $this->type = $type;
        $this->title = $title;
    }

    public function toArray()
    {
        return array_merge(
            $this->extraData,
            array(
                'status' => $this->statusCode,
                'type' => $this->type,
                'title' => $this->title,
            )
        );
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function set($name, $value)
    {
        $this->extraData[$name] = $value;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
