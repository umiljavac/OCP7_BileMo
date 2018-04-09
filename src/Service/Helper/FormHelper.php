<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 30/03/2018
 * Time: 10:12
 */

namespace App\Service\Helper;

use App\Entity\Client;
use App\Entity\Phone;
use App\Entity\User;
use App\Form\Type\ClientRegristrationType;
use App\Form\Type\PhoneType;
use App\Form\Type\UserRegistrationType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class FormHelper
{
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param Phone $phone
     * @return FormInterface
     */
    public function createPhoneForm(Phone $phone)
    {
        $form = $this->createForm(PhoneType::class, $phone);
        return $form;
    }

    /**
     * @param User $user
     * @return FormInterface
     */
    public function createUserRegistrationForm(User $user)
    {
        $form = $this->createForm(UserRegistrationType::class, $user);
        return $form;
    }

    public function createClientRegistrationForm(Client $client)
    {
        $form = $this->createForm(ClientRegristrationType::class, $client);
        return $form;
    }

    /**
     * @param $form
     * @return array
     */
    public function getFormDataErrors($form)
    {
        $errors = $this->getErrorsFromForm($form);
        $data = [
            'type' => 'validation_error',
            'title' => 'There was a validation error',
            'errors' => $errors
        ];
        return $data;
    }

    /**
     * @param $type
     * @param null $data
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createForm($type, $data = null, array $options = array())
    {
        return $this->formFactory->create($type, $data, $options);
    }

    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }
}
