#Sourcemap Properties

This file is a description of the native sourcemap objects, supplychains, stops (points on the map), and hops (lines on the map) as well as suggested default values.

##Supplychain Properties

For common properties of supplychain objects, please see `api.md`.

##Stop Properties

* **id** - A unique incremental id representing the stop.
* **title** - Add the name of the source as you want it to appear in the map bubbles.	
* **placename**	- Insert the place name for the source. 
* **address** - Enter specific address, this will be the Geocode used for the mapped location.		
* **description** - Type the text description of the source here. Can be a fact about procurement, the product, the region, etc.	
* **percentage** - Proportion of Total Volume. Enter the percentage of total sourcing volume coming from this source. This can be the percentage of weight/volume/etc ofthe total product or percentage that this location represents.	
* **color** - This will be the color of the Stop. Choose different colors to represent different layers, facts, or other variable. 	
* **size** - Manual size entry for the stop (in pixels)
* **qty** - Number of units.	
* **unit** - Units, like kg or ea (eaches).
* **weight** - Weight per unit, if other then kg.
* **co2e** - Impact factor (co2e) of the source.
* **urltitle:moreinfo**	- A name for the link to more information on the source.
* **url:moreinfo** - A link for more information on the source.
* **youtube:title** - The title for the youtube video.
* **youtube:link**	- The link to a youtube movie to be embedded.
* **flickr:setid**	- The flickr setid to be embedded.
* **vimeo:title** - The title for the vimeo video.	
* **vimeo:link** - The link to a vimeo movie to be embedded.	

##Hop Properties
* **from** - The id of the stop the hop is coming from.	
* **to** - The id of the stop the hop is going to.
* **description**	- Type the text description of the transport here. Can be a fact about procurement, the product, the region, etc.	
* **color** - This will be the color of the Hop. Choose different colors to represent different layers, facts, or other variables.	
* **transport** - Transport type (like "Air Freight (Regional)")
* **qty** - Number of units traveling.	
* **unit** - Units, like kg or pax (for personal travel).
* **distance** - Distance traveled, if not calculated automatically.
* **co2e** - Impact factor (co2e) of the transport type.