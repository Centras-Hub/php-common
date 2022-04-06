<?php

namespace phpcommon\Utils;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    /**
     * @throws Throwable
     */
    public function accessLevel(int $accessLevel, string $operationType): static
    {
        $this->setHidden($this::fields);
        $this->fillable = [];

        if ($accessLevel < 0 || $accessLevel > max(array_keys($this::accessLevels[$operationType]))) {
            $this->fillable($this::fields);
            $this->setVisible($this::fields);
            $this->makeVisible($this::fields);

            return $this;
        }

        $this->fillable($this::accessLevels[$operationType][$accessLevel]);
        $this->makeVisible($this::accessLevels[$operationType][$accessLevel]);
        $this->setVisible($this::accessLevels[$operationType][$accessLevel]);

        return $this;
    }
}
