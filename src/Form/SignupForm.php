<?php
namespace App\Form;

use App\Entity\User;
use App\Helper\Validator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SignupForm
{
    public $first_name;
    public $last_name;
    public $phone;
    public $id_user;
    public $id_org;
    public $org;
    public $password;
    public $password2;

    public function __construct($data = [])
    {
        foreach ($data as $property => $value) {
            $this->$property = $this->sanitize($value);
        }
    }

    public function sanitize($value)
    {
        return filter_var($value, FILTER_SANITIZE_STRING);
    }

    public function validate()
    {
        if (!Validator::test('simpleString', $this->first_name)) {
            return false;
        }

        if (!Validator::test('simpleString', $this->last_name)) {
            return false;
        }

        if (!Validator::test('simplePhone', $this->phone)) {
            return false;
        }

        if (!Validator::test('password', $this->password)) {
            return false;
        }

        return true;
    }

}