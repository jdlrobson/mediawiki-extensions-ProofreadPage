<?php

/**
 * @group ProofreadPage
 * @covers ProofreadPageLevel
 */
class ProofreadPageLevelTest extends ProofreadPageTestCase {

	public function setUp() {
		parent::setUp();

		$wgGroupPermissions['*']['pagequality'] = false;
		$wgGroupPermissions['user']['pagequality'] = true;
	}

	public function testGetLevel() {
		$level = new ProofreadPageLevel( 1, null );
		$this->assertEquals( 1, $level->getLevel() );
	}

	public function testGetUser() {
		$user = User::newFromName( 'aaa' );
		$level = new ProofreadPageLevel( 1, $user );
		$this->assertEquals( $user, $level->getUser() );
	}

	public function equalsProvider() {
		return array(
			array( new ProofreadPageLevel( 1, null ), new ProofreadPageLevel( 2, null ), false ),
			array( new ProofreadPageLevel( 1, User::newFromName( 'test' ) ), new ProofreadPageLevel( 1, null ), false ),
			array( new ProofreadPageLevel( 1, User::newFromName( 'ater_ir' ) ), new ProofreadPageLevel( 1, User::newFromName( 'ater ir' ) ), true ),
			array( new ProofreadPageLevel( 1, null ), null, false )
		);
	}

	/**
	 * @dataProvider equalsProvider
	 */
	public function testEquals( $a, $b, $equal ) {
		$this->assertEquals( $equal, $a->equals( $b ) );
	}

	public function isChangeAllowedProvider() {
		$testUser = User::newFromName( 'Test' );
		$testUser->addGroup( 'user' );
		$test2User =  User::newFromName( 'Test2' );
		$test2User->addGroup( 'user' );
		$ipUser = User::newFromName( '172.16.254.7', false );

		return array(
			array(
				new ProofreadPageLevel( 1, $testUser ),
				new ProofreadPageLevel( 2, $ipUser ),
				false
			),
			array(
				new ProofreadPageLevel( 1, $testUser ),
				new ProofreadPageLevel( 2, $test2User ),
				true
			),
			array(
				new ProofreadPageLevel( 1, null ),
				new ProofreadPageLevel( 1, $ipUser ),
				true
			),
			array(
				new ProofreadPageLevel( 3, $testUser ),
				new ProofreadPageLevel( 4, $testUser ),
				false
			),
			array(
				new ProofreadPageLevel( 1, $testUser ),
				new ProofreadPageLevel( 4, $test2User ),
				false
			),
			array(
				new ProofreadPageLevel( 1, null ),
				new ProofreadPageLevel( 4, $testUser ),
				false
			),
		);
	}

	/**
	 * @dataProvider isChangeAllowedProvider
	 */
	public function testIsChangeAllowed( $old, $new, $result ) {
		$this->assertEquals( $result, $old->isChangeAllowed( $new ) );
	}

	public function nameProvider() {
		return array(
			array( 'wikiUser', User::newFromName( 'WikiUser' ) ),
			array( '', null ),
			array( '172.16.254.7', User::newFromName( '172.16.254.7', false ) ),
			array( '2001:odb8:ac10:fe10:00:00:00:00', User::newFromName( '2001:odb8:ac10:fe10:00:00:00:00', false ) )
		);
	}

	/**
	 * @dataProvider nameProvider
	 */
	public function testGetUserFromUserName( $name, $user ) {
		$this->assertEquals( $user, ProofreadPageLevel::getUserFromUserName( $name ) );
	}

	/**
	 * @dataProvider nameProvider
	 */
	public function testGetLevelCategoryName() {
		$level = new ProofreadPageLevel( 1, null );
		$this->assertEquals( 'Not proofread', $level->getLevelCategoryName() );
	}
}
