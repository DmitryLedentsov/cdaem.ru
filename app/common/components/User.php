<?php

namespace common\components;

class User extends \nepster\users\components\User
{
    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        if ($this->isGuest || !$this->identity) {
            return 'Guest';
        }

        if (isset($this->identity->profile->name) || isset($this->identity->profile->surname)) {
            return trim(sprintf('%s%s', $this->identity->profile->name ?? '', $this->identity->profile->surname ?? ''));
        }

        return $this->identity->email;
    }
}
