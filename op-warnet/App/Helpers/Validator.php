<?php
class Validator {
    private array $data = [];
    private array $errors = [];
    private ?Database $db = null;

    public function __construct(array $data) {
        $this->data = $data;
    }

    private function addError(string $field, string $message): void {
        $this->errors[$field][] = $message;
    }

    private function getValue(string $field): mixed {
        return $this->data[$field] ?? null;
    }

    public function fails(): bool {
        return !empty($this->errors);
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function required(string $field, string $label = ''): self {
        $value = $this->getValue($field);
        $label = $label ?: ucfirst($field);
        if ($value === null || $value === '') {
            $this->addError($field, "{$label} wajib diisi.");
        }
        return $this;
    }

    public function minLength(string $field, int $length, string $label = ''): self {
        $value = $this->getValue($field);
        $label = $label ?: ucfirst($field);
        if ($value !== null && $value !== '' && mb_strlen($value) < $length) {
            $this->addError($field, "{$label} minimal harus {$length} karakter.");
        }
        return $this;
    }

    public function maxLength(string $field, int $length, string $label = ''): self {
        $value = $this->getValue($field);
        $label = $label ?: ucfirst($field);
        if ($value !== null && $value !== '' && mb_strlen($value) > $length) {
            $this->addError($field, "{$label} maksimal harus {$length} karakter.");
        }
        return $this;
    }

    public function numeric(string $field, string $label = ''): self {
        $value = $this->getValue($field);
        $label = $label ?: ucfirst($field);
        if ($value !== null && $value !== '' && !is_numeric($value)) {
            $this->addError($field, "{$label} harus berupa angka.");
        }
        return $this;
    }

    public function integer(string $field, string $label = ''): self {
        $value = $this->getValue($field);
        $label = $label ?: ucfirst($field);
        if ($value !== null && $value !== '' && filter_var($value, FILTER_VALIDATE_INT) === false) {
            $this->addError($field, "{$label} harus berupa bilangan bulat.");
        }
        return $this;
    }

    public function min(string $field, int $minValue, string $label = ''): self {
        $value = $this->getValue($field);
        $label = $label ?: ucfirst($field);
        if ($value !== null && $value !== '' && is_numeric($value) && $value < $minValue) {
            $this->addError($field, "{$label} minimal harus {$minValue}.");
        }
        return $this;
    }

    public function between(string $field, int $min, int $max, string $label = ''): self {
        $value = $this->getValue($field);
        $label = $label ?: ucfirst($field);
        if ($value !== null && $value !== '' && is_numeric($value) && ($value < $min || $value > $max)) {
            $this->addError($field, "{$label} harus antara {$min} dan {$max}.");
        }
        return $this;
    }

    public function in(string $field, array $allowedValues, string $label = ''): self {
        $value = $this->getValue($field);
        $label = $label ?: ucfirst($field);
        if ($value !== null && $value !== '' && !in_array($value, $allowedValues)) {
            $this->addError($field, "{$label} tidak valid.");
        }
        return $this;
    }

    private function getDb(): Database {
        if ($this->db === null) {
            $this->db = new Database();
        }
        return $this->db;
    }


    public function unique(string $field, string $table, string $column, ?int $exceptId = null, string $label = ''): self {
        $value = $this->getValue($field);
        $label = $label ?: ucfirst($field);
        if ($value !== null && $value !== '') {
            $db = $this->getDb();
            $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = :value";
            $params = [':value' => $value];
            if ($exceptId !== null) {
                $sql .= " AND id != :id";
                $params[':id'] = $exceptId;
            }
            $db->query($sql);
            foreach ($params as $key => $val) {
                $db->bind($key, $val);
            }
            $result = $db->single();
            if ($result && $result['count'] > 0) {
                $this->addError($field, "{$label} '{$value}' sudah digunakan.");
            }
        }
        return $this;
    }

    public function confirmed(string $field, string $label = ''): self {
        $value = $this->getValue($field);
        $confirmField = $field . '_confirmation';
        $confirmValue = $this->getValue($confirmField);
        $label = $label ?: ucfirst($field);
        if ($value !== $confirmValue) {
            $this->addError($confirmField, "Konfirmasi {$label} tidak cocok.");
        }
        return $this;
    }

    public function dateFormat(string $field, string $format = 'Y-m-d', string $label = ''): self {
        $value = $this->getValue($field);
        $label = $label ?: ucfirst($field);
        if ($value !== null && $value !== '') {
            $d = DateTime::createFromFormat($format, $value);
            if (!($d && $d->format($format) === $value)) {
                $this->addError($field, "Format {$label} harus {$format}.");
            }
        }
        return $this;
    }
}