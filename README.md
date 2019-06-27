[![Build Status](https://travis-ci.org/mylk/grain.svg?branch=master)](https://travis-ci.org/mylk/grain)
[![Code Climate](https://codeclimate.com/github/mylk/grain/badges/gpa.svg)](https://codeclimate.com/github/mylk/grain)

Grain
=====

Grain hopes to be a fully-functional MVC framework with the most of the useful goodies other frameworks offer.

You may ask, why would someone make something like this, when there are so many frameworks.

The answer is simple and its "learn".

This document is a small presentation of what Grain supports, what doesn't but want to.

But, first we will refer to where you can find a skeleton application you can use to start building your application over Grain.

Skeleton application
====================

You can get started by cloning the [grain-skeleton](https://github.com/mylk/grain-skeleton/) application.

Using composer you can setup the required dependencies, which are just Grain for now :-)

Follow [grain-skeleton](https://github.com/mylk/grain-skeleton/)'s README for more detailed instructions.

Supports
========

MVC-like architecture
---------------------

Providing a fully-MVC architecture sounds far.

What you can do right now using Grain, is to build your application separating Controllers from Views.

Views are plain PHP files for now, but you can pass variables a bit more elegantly than normal.

Grain also forces you to build your application in a proper directory structure.

Front controllers & Routing
---------------------------

There is a front controller design that will help you build your application in a more elegant and organized way.

The front controllers define the routes and each route will nicely correspond to a "controller action".

You can have different front controllers per environment with different configuration and routes.

Event dispatching
-----------------

Currently the framework dispatches a few events that you can hook into and execute your code, like "post request"

(when the request first arrives to your application) and "pre response" (when the response is about to be sent to the client).

Event listeners are the units of your program that will be executed upon an event happens, which can be easily

defined in a definitions file. You can also create your own events, dispatch them and hook your code into them.

Dependency injection container
------------------------------

Grain has a container to store and serve the units you most commonly use and want to be easily accessible from other units.

The container instantiates those unites only once when requested, and then serves the same instance when requested again.

Those units, called "services" can be easily defined in a definitions file.

Also, services can have dependencies that will be injected into them upon request of the service.

You can define the scope of a service, if they should be accessible from your controllers or just from other services as dependencies.

A (simple) template engine
--------------------------

The (simple) template engine can render PHP templates or plain text templates (named TPL files for convenience).

PHP files can evaluate any valid PHP expression normally, *but you shouldn't put any logic in your templates*.

For that reason, its better to use the TPL templates, which just contain variables prepended with the % sign

(ex. %firstName) that will be interpolated by the values passed to the template engine.

Whishes to support
==================

Below follow a few features that I think would be cool to be available in Grain.

Database abstraction
--------------------

Avoid ugly queries in your code and provide a way to access and modify your database data more easily.
