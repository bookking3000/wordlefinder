<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class WordFinderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $wordlyInputsConfig = [
            'constraints' => new Length(['min' => 0, 'max' => 1]),
            'label' => false,
            'required' => false,
            'attr' => ['class' => 'char-inp', 'maxlength' => 1]
        ];

        $builder->add('position_1', TextType::class, $wordlyInputsConfig);
        $builder->add('position_2', TextType::class, $wordlyInputsConfig);
        $builder->add('position_3', TextType::class, $wordlyInputsConfig);
        $builder->add('position_4', TextType::class, $wordlyInputsConfig);
        $builder->add('position_5', TextType::class, $wordlyInputsConfig);
        $builder->add('position_6', TextType::class, $wordlyInputsConfig);

        $builder->add('notAllowedChars', TextType::class, [
            'required' => false,
            'label' => 'Buchstaben welche nicht vorkommen dürfen:',
            'attr' => [
                'class' => 'form-control'
            ]
        ]);

        $builder->add('charsWhichAreIn', TextType::class, [
            'required' => false,
            'label' => 'Buchstaben welche vorkommen müssen:',
            'attr' => [
                'class' => 'form-control'
            ]
        ]);

        $builder->add('save', SubmitType::class, [
            'label' => 'Wörter finden!',
            'attr' => [
                'class' => 'btn btn-primary mt-3'
            ]
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'form_solver',
        ]);
    }
}
