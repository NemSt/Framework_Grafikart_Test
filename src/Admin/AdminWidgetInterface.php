<?php
namespace App\Admin;

interface AdminWidgetInterface
{
    //public function getPosition(): int; //pour déterminer l'ordre d'affichage des widgets

    public function render(): string;

    public function renderMenu(): string;
}
