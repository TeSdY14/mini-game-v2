<?php


class Magicien extends Personnage
{

    public function lancerUnSort(Personnage $perso): int
    {
        $this->definirAtout();

        if ($perso->id === $this->id) {
            return self::CEST_MOI;
        }

        if ($this->atout == 0) {
            return self::PAS_DE_MAGIE;
        }

        if ($this->estEndormi()) {
            return self::PERSO_ENDORMI;
        }

        $perso->setTimeEndormi(time() + ($this->atout * 6) * 3600);

        return self::PERSO_ENSORCELE;
    }

}