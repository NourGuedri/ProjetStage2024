<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Entity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdminRepository")
 */

#[Entity]

// EL inheritance ma ta3melch foreign key ama sta3meltha 3la khater 
// the Admin is a type of User with all the properties of a User plus some additional properties
class Admin extends User
{

    

}
?>