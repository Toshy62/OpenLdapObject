<?php

namespace OpenLdapObject\Tests\Manager;


use OpenLdapObject\LdapClient\Connection;
use OpenLdapObject\Manager\EntityManager;
use OpenLdapObject\OpenLdapObject;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityManager
     */
    private $em;

    public function setUp()
    {
        $connect = new Connection(LDAP_HOST, LDAP_PORT);
        $connect->identify(LDAP_USER, LDAP_PASSWORD);
        $client = $connect->connect();
        $client->setBaseDn(LDAP_BASE_DN);
        try {
            EntityManager::addEntityManager('default', $client);
        } catch (\InvalidArgumentException $e) {

        }
        $this->em = EntityManager::getEntityManager();
    }

    public function testQuery()
    {
        $people = $this->em->getRepository('\OpenLdapObject\Tests\Manager\People')->find('pdeparis');

        $this->assertEquals($people->getUid(), 'pdeparis');
        $this->assertEquals($people->getSn(), 'Deparis');
        $this->assertEquals($people->getCn(), 'Pierre Deparis');
        $this->assertEquals($people->getGivenName(), 'Pierre');
        $this->assertEquals($people->getMail(), 'pierre.deparis@example.com');
        $this->assertEquals($people->getTelephoneNumber()->toArray(), array('03 00 00 00 01', '04 00 00 00 01'));
    }

    public function testFindByQuery()
    {
        $people = $this->em->getRepository('\OpenLdapObject\Tests\Manager\People')->findBy(['uid' => 'pdeparis', 'telephoneNumber' => '03 00 00 00 01']);
        $people = $people[0];
        $this->assertEquals($people->getUid(), 'pdeparis');
        $this->assertEquals($people->getSn(), 'Deparis');
        $this->assertEquals($people->getCn(), 'Pierre Deparis');
        $this->assertEquals($people->getGivenName(), 'Pierre');
        $this->assertEquals($people->getMail(), 'pierre.deparis@example.com');
        $this->assertEquals($people->getTelephoneNumber()->toArray(), array('03 00 00 00 01', '04 00 00 00 01'));
    }

    public function testFlush()
    {
        $newPeople = new People();
        $newPeople
            ->setUid('mdubois')
            ->setCn('Maurice Dubois')
            ->setSn('Dubois')
            ->setGivenName('Maurice')
            ->setMail('maurice.dubois@example.com')
            ->addTelephoneNumber('03 00 00 00 02');

        $this->em->persist($newPeople);
        $this->em->flush();

        $people = $this->em->getRepository('\OpenLdapObject\Tests\Manager\People')->find('mdubois');
        $people->setUid('maurice.dubois');

        $this->em->persist($people);
        $this->em->flush();

        $people = $this->em->getRepository('\OpenLdapObject\Tests\Manager\People')->find('maurice.dubois');
        $this->em->remove($people);
        $this->em->flush();
    }

    public function testRelation()
    {
        $org = $this->em->getRepository('\OpenLdapObject\Tests\Manager\Organisation')->find('state');

        $uid = array();
        foreach ($org->getMember() as $value) {
            $uid[] = $value->getUid();
        }
        $this->assertEquals($uid, array('pdeparis', 'mdupont'));
    }

    public function testRelationSingle()
    {
        $org = $this->em->getRepository('\OpenLdapObject\Tests\Manager\OrganisationSingle')->find('single-member');

        $this->assertEquals(get_class($org->getMember()), 'OpenLdapObject\Tests\Manager\People');
        $this->assertEquals($org->getMember()->getUid(), 'mdupont');
    }

    public function testNewWithEntityCollection()
    {
        $org = new Organisation();
        $org->setCn('test');
        $org->addMember($this->em->getRepository('\OpenLdapObject\Tests\Manager\People')->find('pdeparis'));

        $this->em->persist($org);
        $this->em->flush();

        $this->em->remove($org);
        $this->em->flush();
    }

    /**
     * @expectedException OpenLdapObject\Exception\InvalidHydrateException
     */
    public function testWithStrict()
    {
        $user = $this->em->getRepository('\OpenLdapObject\Tests\Manager\People')->find('jdoe');
    }

    public function testWithNoStrict()
    {
        $user = $this->em->getRepository('\OpenLdapObject\Tests\Manager\PeopleNonStrict')->find('jdoe');

        $this->assertEquals($user->getGivenName(), 'John');
    }

    public function testWithDisableGlobalStrict()
    {
        OpenLdapObject::disableStrictMode();
        $user = $this->em->getRepository('\OpenLdapObject\Tests\Manager\People')->find('jdoe');

        $this->assertEquals($user->getGivenName(), 'John');
        OpenLdapObject::enableStrictMode();
    }

    public function testAddWithObjectClass()
    {
        $org = new Organisation();
        $org->setCn('organisation')
            ->addObjectClass('labeledUriObject')
            ->addMember($this->em->getRepository('\OpenLdapObject\Tests\Manager\People')->find('pdeparis'));

        $this->assertEquals($org->getObjectClass()->toArray(), array('groupOfNames', 'top', 'labeledUriObject'));

        $this->em->persist($org);
        $this->em->flush();

        $structure = $this->em->getRepository('\OpenLdapObject\Tests\Manager\Organisation')->find('organisation');
        $this->assertEquals($structure->getObjectClass()->toArray(), array('groupOfNames', 'top', 'labeledUriObject'));

        $this->em->remove($structure);
        $this->em->flush();
    }

    /**
     * @expectedException OpenLdapObject\Exception\BadRelationException
     */
    public function testBadRelation()
    {
        $org = $this->em->getRepository('\OpenLdapObject\Tests\Manager\Organisation')->find('bad-relation');

        $listUid = array();
        foreach ($org->getMember() as $member) {
            $listUid[] = $member->getUid();
        }

    }

    public function testBadRelationIgnore()
    {
        $org = $this->em->getRepository('\OpenLdapObject\Tests\Manager\OrganisationBadRelation')->find('bad-relation');

        $this->assertEquals(count($org->getMember()), 2);

        $listUid = array();
        foreach ($org->getMember() as $member) {
            $listUid[] = $member->getUid();
        }

        $this->assertEquals($listUid, array('pdeparis', 'mdupont'));
    }
}
 
