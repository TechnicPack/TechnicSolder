<?php

class UpdateCheckerTest extends TestCase {

	public function testIsExecEnabled()
	{
		$this->assertTrue(UpdateUtils::isExecEnabled());
	}

	public function testIsGitInstalled()
	{
		$this->assertTrue(UpdateUtils::isGitInstalled());
	}

	public function testIsGitRepo()
	{
		$this->assertTrue(UpdateUtils::isGitRepo());
	}
}