<?php
/**
 * File containing the LegacyMapperTest class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishLegacyBundle\Tests\SiteAccess;

use eZ\Publish\Legacy\Tests\LegacyBasedTestCase,
    eZ\Bundle\EzPublishLegacyBundle\SiteAccess\LegacyMapper,
    eZ\Publish\MVC\SiteAccess,
    eZ\Publish\MVC\Event\PostSiteAccessMatchEvent,
    \eZSiteAccess;

class LegacyMapperTest extends LegacyBasedTestCase
{
    /**
     * @dataProvider siteAccessMatchProvider
     * @covers \eZ\Bundle\EzPublishLegacyBundle\SiteAccess\LegacyMapper::__construct
     * @covers \eZ\Bundle\EzPublishLegacyBundle\SiteAccess\LegacyMapper::onSiteAccessMatch
     */
    public function testOnSiteAccessMatch( $pathinfo, $semanticPathinfo, SiteAccess $siteaccess, $expectedAccess )
    {
        $request = $this->getRequestMock();
        $request
            ->expects( $this->any() )
            ->method( 'getPathInfo' )
            ->will( $this->returnValue( $pathinfo ) );
        $request->attributes->set( 'semanticPathinfo', $semanticPathinfo );

        $mapper = new LegacyMapper;
        $mapper->onSiteAccessMatch(
            new PostSiteAccessMatchEvent(
                $siteaccess,
                $request
            )
        );
        self::assertSame( $expectedAccess, $request->attributes->get( 'legacySiteaccess' ) );
    }

    public function siteAccessMatchProvider()
    {
        return array(
            array(
                '/some/pathinfo',
                '/some/pathinfo',
                new SiteAccess( 'foo', 'default' ),
                array(
                    'name'      => 'foo',
                    'type'      => 1,
                    'uri_part'  => array()
                )
            ),
            array(
                '/env/matching',
                '/env/matching',
                new SiteAccess( 'foo', 'env' ),
                array(
                    'name'      => 'foo',
                    'type'      => 7,
                    'uri_part'  => array()
                )
            ),
            array(
                '/urimap/matching',
                '/urimap/matching',
                new SiteAccess( 'foo', 'uri:map' ),
                array(
                    'name'      => 'foo',
                    'type'      => 2,
                    'uri_part'  => array()
                )
            ),
            array(
                '/foo/urimap/matching',
                '/urimap/matching',
                new SiteAccess( 'foo', 'uri:map' ),
                array(
                    'name'      => 'foo',
                    'type'      => 2,
                    'uri_part'  => array( 'foo' )
                )
            ),
            array(
                '/urielement/matching',
                '/urielement/matching',
                new SiteAccess( 'foo', 'uri:element' ),
                array(
                    'name'      => 'foo',
                    'type'      => 2,
                    'uri_part'  => array()
                )
            ),
            array(
                '/foo/bar/urielement/matching',
                '/urielement/matching',
                new SiteAccess( 'foo', 'uri:element' ),
                array(
                    'name'      => 'foo',
                    'type'      => 2,
                    'uri_part'  => array( 'foo', 'bar' )
                )
            ),
            array(
                '/foo/bar/baz/urielement/matching',
                '/urielement/matching',
                new SiteAccess( 'foo', 'uri:element' ),
                array(
                    'name'      => 'foo',
                    'type'      => 2,
                    'uri_part'  => array( 'foo', 'bar', 'baz' )
                )
            ),
            array(
                '/uritext/matching',
                '/uritext/matching',
                new SiteAccess( 'foo', 'uri:text' ),
                array(
                    'name'      => 'foo',
                    'type'      => 2,
                    'uri_part'  => array()
                )
            ),
            array(
                '/uriregex/matching',
                '/uriregex/matching',
                new SiteAccess( 'foo', 'uri:regexp' ),
                array(
                    'name'      => 'foo',
                    'type'      => 2,
                    'uri_part'  => array()
                )
            ),
            array(
                '/hostmap/matching',
                '/hostmap/matching',
                new SiteAccess( 'foo', 'host:map' ),
                array(
                    'name'      => 'foo',
                    'type'      => 4,
                    'uri_part'  => array()
                )
            ),
            array(
                '/hostelement/matching',
                '/hostelement/matching',
                new SiteAccess( 'foo', 'host:element' ),
                array(
                    'name'      => 'foo',
                    'type'      => 4,
                    'uri_part'  => array()
                )
            ),
            array(
                '/hosttext/matching',
                '/hosttext/matching',
                new SiteAccess( 'foo', 'host:text' ),
                array(
                    'name'      => 'foo',
                    'type'      => 4,
                    'uri_part'  => array()
                )
            ),
            array(
                '/hostregex/matching',
                '/hostregex/matching',
                new SiteAccess( 'foo', 'host:regexp' ),
                array(
                    'name'      => 'foo',
                    'type'      => 4,
                    'uri_part'  => array()
                )
            ),
            array(
                '/port/matching',
                '/port/matching',
                new SiteAccess( 'foo', 'port' ),
                array(
                    'name'      => 'foo',
                    'type'      => 3,
                    'uri_part'  => array()
                )
            ),
            array(
                '/custom/matching',
                '/custom/matching',
                new SiteAccess( 'foo', 'custom_match' ),
                array(
                    'name'      => 'foo',
                    'type'      => 10,
                    'uri_part'  => array()
                )
            ),
        );
    }

    /**
     * @param $methodsToMock
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\HttpFoundation\Request
     */
    private function getRequestMock( array $methodsToMock = array() )
    {
        return $this
            ->getMockBuilder( 'Symfony\\Component\\HttpFoundation\\Request' )
            ->setMethods( array_merge( array( 'getPathInfo' ), $methodsToMock ) )
            ->getMock();
    }
}
