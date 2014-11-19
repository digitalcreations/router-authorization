<?php

namespace DC\Router\Authorization;

class AuthorizeTag extends \phpDocumentor\Reflection\DocBlock\Tag {

    /**
     * @var string[]
     */
    private $roles = [];

    const REGEX_SPLIT = '/[^a-zA-Z0-9_\x7f-\xff$]+/im';

    /**
     * From http://no2.php.net/language.variables.basics
     */
    const REGEX_VARIABLE = '/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/';

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        parent::setContent($content);

        $parts = preg_split(self::REGEX_SPLIT, $content, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($parts as $part) {
            if (preg_match(self::REGEX_VARIABLE, $part, $matches)) {
                $this->roles[] = trim($matches[0], ' ');
            }
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles() {
        return $this->roles;
    }
} 