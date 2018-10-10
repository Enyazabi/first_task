<?php

class DependencyException extends Exception
{
}

class MissingResembleNameException extends DependencyException
{
}

class MissingDependencyInName extends DependencyException
{
}

class MissingJoinedDependencyInKey extends DependencyException
{
}

class ItselfDependency extends DependencyException
{
}

class LoopDependencyException extends DependencyException
{
}