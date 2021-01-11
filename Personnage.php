<?php


class Personnage
{
    protected $id;
    protected $nom;
    protected $atout;
    protected $timeEndormi;
    protected $degats;
    protected $type;


    // Constante si on se frappe soit même
    const CEST_MOI = 1;
    const PERSO_TUE = 2;
    const PERSO_FRAPPE = 3;
    const PERSO_ENSORCELE = 4;
    const PAS_DE_MAGIE = 5;
    const PERSO_ENDORMI = 6;

    public function __construct(array $data)
    {
        $this->hydrate($data);
        $this->type = strtolower(static::class);
    }

    public function estEndormi(): bool
    {
        echo '<br> $this->timeEndormi : ', $this->timeEndormi, ' // Time : ', time(), '<br>';
        return $this->timeEndormi > time();
    }

    /**
     * @param Personnage $enemy
     * @return int
     */
    public function frapper(Personnage $enemy): int
    {
        // checker que l'on tape bien un ennemi
        if ($enemy->id === $this->id) {
            return self::CEST_MOI;
        }

        if ($this->estEndormi()) {
            return self::PERSO_ENDORMI;
        }
        // indiquer au personnage frapper ses dégâts
        return $enemy->recevoirDegats();
    }

    public function hydrate(array $data) {
        foreach ($data as $key => $value) {
            $method = 'set'.ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getDegats(): int
    {
        return $this->degats;
    }

    public function setDegats(int $degats)
    {
        $this->degats = $degats;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getAtout(): int
    {
        return $this->atout;
    }

    /**
     * @param mixed $atout
     */
    public function setAtout(int $atout): void
    {
        if ($atout >= 0 && $atout <= 100) {
            $this->atout = $atout;
        }
    }

    /**
     * @return mixed
     */
    public function getTimeEndormi()
    {
        return $this->timeEndormi;
    }

    /**
     * @param mixed $timeEndormi
     */
    public function setTimeEndormi(int $timeEndormi): void
    {
        $this->timeEndormi = $timeEndormi;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function nomValid(): bool
    {

        return !empty($this->nom);
    }

    public function recevoirDegats(): int {
        // augmentation de 5 des dégâts
        $this->degats += 5;
        // si dégâts = 100 (ou plus) le personnage est mort
        if ($this->degats >= 100) {
            return self::PERSO_TUE;
        }
        // Sinon : on retourne la valeur signifiant que le personnage a bien été frappé
        return self::PERSO_FRAPPE;
    }

    public function reveil(): string
    {
        $secondes = $this->timeEndormi;
        $secondes -= time();

        $heures = floor($secondes / 3600);
        $secondes -= $heures * 3600;
        $minutes = floor($secondes / 60);
        $secondes -= $minutes * 60;

        $heures .= $heures <= 1 ? ' heure' : ' heures';
        $minutes .= $minutes <= 1 ? ' minute' : ' minutes';
        $secondes .= $secondes <= 1 ? ' seconde' : ' secondes';

        return $heures . ', ' . $minutes . ', ' . $secondes;
    }

    protected function definirAtout() {
        if ($this->degats >= 0 && $this->degats <= 25) {
            $this->atout = 4;
        } elseif ($this->degats > 25 && $this->degats <= 50) {
            $this->atout = 3;
        } elseif ($this->degats > 50 && $this->degats <= 75) {
            $this->atout = 2;
        } elseif ($this->degats > 75 && $this->degats <= 90) {
            $this->atout = 1;
        } else {
            $this->atout = 0;
        }
    }
}