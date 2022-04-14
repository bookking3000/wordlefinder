<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class WordFinderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $wordlyInputsConfig = [
            'constraints' => new Length(['min' => 0, 'max' => 1]),
            'label' => false,
            'required' => false,
            'attr' => [
                'class' => 'char-inp',
                'maxlength' => 1,
            ]
        ];

        for ($i = 1; $i <= 6; $i++) {
            $builder->add("position_$i", TextType::class, $wordlyInputsConfig);
        }


        $builder->add('notAllowedChars', TextType::class, [
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
            'label' => 'Buchstaben welche nicht vorkommen dürfen:',
            'attr' => [
                'class' => 'form-control'
            ]
        ]);

        $builder->add('charsWhichAreIn', TextType::class, [
            'required' => true,
            'label' => 'Buchstaben welche vorkommen müssen:',
            'attr' => [
                'class' => 'form-control'
            ]
        ]);


        $builder->add('indexOfForbiddenChars', TextType::class, [
            'required' => true,
            'label_html' => true,
            'label' => '<b>Neu:</b> Buchstaben welche an Stelle <span id="char_position">X</span> nicht vorkommen dürfen.',
            'attr' => [
                'class' => 'form-control'
            ]
        ]);


        $builder->add('save', SubmitType::class, [
            'label' => 'Wörter finden',
            'attr' => [
                'class' => 'btn btn-primary mt-3'
            ]
        ]);

        $builder->add('reset', ResetType::class, [
            'label' => 'Formular leeren',
            'attr' => [
                'class' => 'btn btn-primary mt-3'
            ]
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'form_solver',
        ]);
    }
}
