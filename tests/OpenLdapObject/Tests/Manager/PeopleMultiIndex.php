<?php

namespace OpenLdapObject\Tests\Manager;

use OpenLdapObject\Annotations as OLO;

/**
 * @OLO\Dn(ou="people")
 */
class PeopleMultiIndex {
    /**
     * @OLO\Column(type="string")
     * @OLO\Index
     */
    private $uid;

    /**
     * @OLO\Column(type="string")
     * @OLO\Index
     */
    private $cn;

    /**
     * @OLO\Column(type="string")
     */
    private $sn;

    /**
     * @OLO\Column(type="string")
     */
    private $givenName;

    /**
     * @OLO\Column(type="string")
     */
    private $mail;

    /**
     * @OLO\Column(type="array")
     */
    private $telephoneNumber;
} 