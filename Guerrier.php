<?php


class Guerrier extends Personnage
{

    public function recevoirDegats(): int
    {
        $this->definirAtout();
        $this->degats += 5 - $this->atout;

        if ($this->degats >= 100) {
            return self::PERSO_TUE;
        }

        return self::PERSO_FRAPPE;
    }
}