<?php


class PersonnagesManager
{

    /**
     * @var PDO
     */
    private $_db;

    public function __construct(PDO $db) {
        $this->_db = $db;
    }

    public function add(Personnage $perso) {
        $q = $this->_db->prepare('INSERT INTO personnages_v2(nom, type) VALUES(:nom, :type)');
        $q->bindValue(':nom',$perso->getNom());
        $q->bindValue(':type', $perso->getType());

        $q->execute();

        $perso->hydrate([
            'id' => $this->_db->lastInsertId(),
            'degats' => 0,
            'atout' => 0,
            'timeEndormi' => 0,
        ]);
    }

    public function count() {
        return $this->_db->query('SELECT COUNT(*) FROM personnages_v2')->fetchColumn();
    }

    public function delete(Personnage $perso) {
        $this->_db->exec('DELETE FROM personnages_v2 WHERE id = ' . $perso->getId());
    }

    public function exists($info): bool
    {
        if (is_int($info)) {
            return (bool) $this->_db->query('SELECT COUNT(*) FROM personnages_v2 WHERE id='.$info)->fetchColumn();
        }

        $q = $this->_db->prepare('SELECT COUNT(*) FROM personnages_v2 WHERE nom=:nom');
        $q->execute([':nom' => $info]);
        return (bool) $q->fetchColumn();
    }

    public function get($info): ?Personnage
    {
        if (is_int($info)) {
            $q = $this->_db->query("SELECT id, nom, degats, timeEndormi, type, atout FROM personnages_v2 WHERE id=" . $info);
        } else {
            $q = $this->_db->prepare("SELECT id, nom, degats, timeEndormi, type, atout FROM personnages_v2 WHERE nom=:nom");
            $q->execute([':nom' => $info]);
        }

        $perso = $q->fetch(PDO::FETCH_ASSOC);

        switch($perso['type']) {
            case 'guerrier': return new Guerrier($perso);
            case 'magicien': return new Magicien($perso);
            default: return null;
        }
    }

    public function getList(?string $nom = null): array
    {
        $persos = [];

        if ($nom) {
            $q = $this->_db->prepare("SELECT id, nom, degats, timeEndormi, type, atout FROM personnages_v2 WHERE nom <> :nom ORDER BY nom");
            $q->execute([':nom' => $nom]);
        } else {
            $q = $this->_db->query("SELECT id, nom, degats, timeEndormi, type, atout FROM personnages_v2 ORDER BY nom");
        }
        while($donnees = $q->fetch(PDO::FETCH_ASSOC)) {

            switch ($donnees['type']) {
                case 'guerrier': $persos[] = new Guerrier($donnees); break;
                case 'magicien': $persos[] = new Magicien($donnees); break;
            }
        }

        return $persos;
    }

    public function update(Personnage $perso) {
        $q = $this->_db->prepare("UPDATE personnages_v2 SET degats = :degats, timeEndormi = :timeEndormi, atout = :atout WHERE id=:id");
        $q->bindValue(':degats', $perso->getDegats(), PDO::PARAM_INT);
        $q->bindValue(':timeEndormi', $perso->getTimeEndormi(), PDO::PARAM_INT);
        $q->bindValue(':atout', $perso->getAtout(), PDO::PARAM_INT);
        $q->bindValue(':id', $perso->getId(), PDO::PARAM_INT);

        $q->execute();
    }

}