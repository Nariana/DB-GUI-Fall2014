# ************************************************************
# Sequel Pro SQL dump
# Version 4135
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: localhost (MySQL 5.5.38)
# Database: PantryQuest
# Generation Time: 2014-10-27 22:31:40 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP DATABASE IF EXISTS PantryQuest;
CREATE DATABASE PantryQuest;
USE PantryQuest;
# Dump of table filter
# ------------------------------------------------------------

DROP TABLE IF EXISTS `filter`;

CREATE TABLE `filter` (
  `filterID` int(11) unsigned NOT NULL,
  `method` varchar(30) DEFAULT NULL,
  `glutenFree` tinyint(1) DEFAULT NULL,
  `vegetarian` tinyint(1) DEFAULT NULL,
  `vegan` tinyint(1) DEFAULT NULL,
  `noNuts` tinyint(1) DEFAULT NULL,
  `lactoseFree` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`filterID`),
  CONSTRAINT `recipeID` FOREIGN KEY (`filterID`) REFERENCES `recipe` (`recipeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `filter` WRITE;
/*!40000 ALTER TABLE `filter` DISABLE KEYS */;

INSERT INTO `filter` (`filterID`, `method`, `glutenFree`, `vegetarian`, `vegan`, `noNuts`, `lactoseFree`)
VALUES
	(1,'oven',0,1,0,1,0);

/*!40000 ALTER TABLE `filter` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ingredient
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ingredient`;

CREATE TABLE `ingredient` (
  `foodName` varchar(30) DEFAULT NULL,
  KEY `foodName` (`foodName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `ingredient` WRITE;
/*!40000 ALTER TABLE `ingredient` DISABLE KEYS */;

INSERT INTO `ingredient` (`foodName`)
VALUES
	('all-purpose flour'),
	('almond essence'),
	('amaretti'),
	('anchovy'),
	('anise'),
	('apple juice'),
	('artichoke'),
	('artichoke'),
	('artichoke heart'),
	('asparagus'),
	('asparagus spear'),
	('aubergine'),
	('avocado'),
	('bacon'),
	('baguette'),
	('baked beans'),
	('baking soda'),
	('balsamic vinegar'),
	('bamboo'),
	('banana'),
	('bap'),
	('basil'),
	('bay leaf'),
	('bay leaves'),
	('bbq sauce'),
	('bean'),
	('beef'),
	('beef brisket'),
	('beef mince'),
	('beef stock'),
	('bell pepper'),
	('berries'),
	('berry'),
	('bicarbonate of soda'),
	('biscuit'),
	('biscuit'),
	('black olive'),
	('black pepper'),
	('black-eyed peas'),
	('blueberries'),
	('blueberry'),
	('bonnet chilli'),
	('bouillon'),
	('bourbon'),
	('braising steak'),
	('bran'),
	('brandy'),
	('bread'),
	('breadcrumbs'),
	('brie'),
	('brine'),
	('broccoli'),
	('brown rice'),
	('brown sauce'),
	('brown sugar'),
	('buckwheat'),
	('buffalo'),
	('bun'),
	('butter'),
	('butter'),
	('buttermilk'),
	('butternut'),
	('butternut squash'),
	('butterscotch'),
	('cabbage'),
	('cake'),
	('candies'),
	('candy'),
	('cannellini'),
	('canola oil'),
	('caper'),
	('caramel'),
	('caraway seed'),
	('cardamom'),
	('carrot'),
	('carrot'),
	('carrot'),
	('cashew'),
	('caster sugar'),
	('cayenne pepper'),
	('celeriac'),
	('celery'),
	('cereal'),
	('champagne'),
	('chard'),
	('cheddar cheese'),
	('cheese'),
	('cherries'),
	('cherry'),
	('cherry tomatoe'),
	('chestnut'),
	('chicken'),
	('chicken'),
	('chicken breast'),
	('chicken drumstick'),
	('chicken leg'),
	('chicken stock'),
	('chicken thigh'),
	('chickpea'),
	('chicory'),
	('chile'),
	('chilli'),
	('chillies'),
	('chip'),
	('chipotle'),
	('chive'),
	('chive'),
	('chocolate'),
	('chop'),
	('choy'),
	('chutney'),
	('ciabatta'),
	('cider'),
	('cinnamon'),
	('clam'),
	('clarified butter'),
	('clove'),
	('cocoa'),
	('coconut'),
	('coconut milk'),
	('cod'),
	('coffee'),
	('coleslaw'),
	('condensed milk'),
	('coriander'),
	('coriander leaves'),
	('corn'),
	('cornflour'),
	('cornmeal'),
	('courgette'),
	('couscous'),
	('cranberries'),
	('cranberry'),
	('cranberry juice'),
	('cream'),
	('creme de cassis'),
	('crisp'),
	('crï¾me fraiche'),
	('crust'),
	('cucumber'),
	('cumin'),
	('custard'),
	('dijon mustard'),
	('dill'),
	('double cream'),
	('duck'),
	('edamame bean'),
	('egg'),
	('egg'),
	('espresso'),
	('fennel'),
	('feta'),
	('fig'),
	('fillet steak'),
	('filo pastry'),
	('fish'),
	('flank'),
	('flour'),
	('flour'),
	('focaccia'),
	('foie gras'),
	('fruit'),
	('fudge'),
	('garam masala'),
	('garlic'),
	('gelatine'),
	('gin'),
	('ginger'),
	('glucose'),
	('goat'),
	('goat\'s cheese'),
	('golden syrup'),
	('gorgonzola'),
	('gouda'),
	('grape'),
	('grape'),
	('grapefruit'),
	('grapeseed'),
	('greek yoghurt'),
	('green'),
	('green olive'),
	('green pepper'),
	('gruyere'),
	('gruyere cheese'),
	('habanero sauce'),
	('haddock'),
	('ham'),
	('harissa'),
	('herbs'),
	('hoisin'),
	('hoisin sauce'),
	('honey'),
	('honeydew melon'),
	('horseradish'),
	('iceberg lettuce'),
	('icing sugar'),
	('jalapeno pepper'),
	('juice'),
	('julienne'),
	('kalamata olives'),
	('ketchup'),
	('kidney bean'),
	('lager'),
	('lamb'),
	('lard'),
	('lardons'),
	('lasagne'),
	('lavender'),
	('leek'),
	('lemon'),
	('lemongrass'),
	('lentil'),
	('lettuce'),
	('lime'),
	('limoncello'),
	('lobster'),
	('loin'),
	('lychee'),
	('macadamia nut'),
	('malt'),
	('mangetout'),
	('mango'),
	('mangoes'),
	('maple syrup'),
	('marinara'),
	('marshmallow'),
	('marzano'),
	('mascarpone'),
	('mayonnaise'),
	('meat'),
	('melon'),
	('milk'),
	('mince'),
	('mint'),
	('miso soup'),
	('molasses'),
	('monterey jack'),
	('mozzarella'),
	('mushroom'),
	('mustard'),
	('natural yoghurt'),
	('new potato'),
	('noodle'),
	('nut'),
	('nutella'),
	('nutmeg'),
	('oat'),
	('oil'),
	('okra'),
	('olive'),
	('olive oil'),
	('onion'),
	('onion ring'),
	('orange'),
	('orange essence'),
	('orange juice'),
	('oregano'),
	('oyster'),
	('pak choi'),
	('pancetta'),
	('panko'),
	('papaya'),
	('paprika'),
	('parma ham'),
	('parmesan'),
	('parmigiano'),
	('parmigiano-reggiano'),
	('parsley'),
	('parsnip'),
	('passion fruit'),
	('pasta'),
	('Pasta shell'),
	('pastry'),
	('pea'),
	('pea'),
	('peach'),
	('peaches'),
	('peanut'),
	('peanut'),
	('pear'),
	('pecan'),
	('pecorino'),
	('penne'),
	('penne pasta'),
	('pepper'),
	('peppercorn'),
	('peppers'),
	('pesto'),
	('pickle'),
	('pickled gherkin'),
	('pickled onion'),
	('pickled red onion'),
	('pie'),
	('pimento pepper'),
	('pine nut'),
	('pineapple'),
	('pistachio'),
	('pita'),
	('pitta'),
	('pitted olive'),
	('pizza'),
	('plantain'),
	('plum'),
	('plum tomato'),
	('plum tomatoes'),
	('poblano'),
	('pod'),
	('polenta'),
	('pomegranate'),
	('popcorn'),
	('pork'),
	('Pork chop'),
	('porridge'),
	('potato'),
	('potatoes'),
	('prawn'),
	('prosciutto'),
	('prosciutto ham'),
	('provolone'),
	('pumpkin'),
	('quail'),
	('radicchio'),
	('radish'),
	('radishes'),
	('rapeseed oil'),
	('raspberries'),
	('raspberry'),
	('red onion'),
	('red onion'),
	('red pepper'),
	('red wine vinegar'),
	('relish'),
	('rhubarb'),
	('rib'),
	('rice'),
	('ricotta'),
	('rocket'),
	('roll'),
	('rose'),
	('rosemary'),
	('russet'),
	('saffron'),
	('sage'),
	('sake'),
	('salad'),
	('salami'),
	('salmon'),
	('salmon flake'),
	('salsa'),
	('salt'),
	('sauce'),
	('sausage'),
	('savoy cabbage'),
	('scallop'),
	('schnapps'),
	('self raising flour'),
	('semolina'),
	('serrano ham'),
	('shallot'),
	('shaohsing'),
	('sherry'),
	('shiitake'),
	('shiitake mushroom'),
	('shortcrust pastry'),
	('single cream'),
	('sirloin'),
	('sirloin steak'),
	('slaw'),
	('smoky bacon'),
	('snapper'),
	('sour cream'),
	('sourdough'),
	('soy sauce'),
	('spaghetti'),
	('spice'),
	('spinach'),
	('sponge'),
	('spring onion'),
	('sprout'),
	('squash'),
	('squid'),
	('sriracha'),
	('steak'),
	('stock'),
	('stout'),
	('strawberries'),
	('strawberry'),
	('stuffing'),
	('sugar'),
	('sugar'),
	('sultana'),
	('sun dried tomato'),
	('sun-dried tomato'),
	('sunflower oil'),
	('sweet pepper'),
	('sweetcorn'),
	('sweets'),
	('swordfish'),
	('tangerine'),
	('tapioca'),
	('tarragon'),
	('tartar'),
	('tea'),
	('tequila'),
	('thyme'),
	('toast'),
	('toffee'),
	('tofu'),
	('tomatillos'),
	('tomato'),
	('tomato puree'),
	('tomatoes'),
	('tortilla'),
	('treacle'),
	('tuna'),
	('tuna steak'),
	('turkey'),
	('turkey breast'),
	('turkey thigh'),
	('turmeric'),
	('turnip'),
	('vanilla'),
	('vanilla essence'),
	('vanilla pod'),
	('veal'),
	('vegetable oil'),
	('vermouth'),
	('vinaigrette'),
	('vinegar'),
	('vodka'),
	('waffle'),
	('walnut'),
	('watercress'),
	('watermelon'),
	('white rice'),
	('white wine vinegar'),
	('wholegrain pasta'),
	('wine'),
	('worcestershire sauce'),
	('yeast'),
	('yellow pepper'),
	('yoghurt'),
	('yogurt');

/*!40000 ALTER TABLE `ingredient` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table recipe
# ------------------------------------------------------------

DROP TABLE IF EXISTS `recipe`;

CREATE TABLE `recipe` (
  `recipeID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `recipeName` varchar(30) DEFAULT NULL,
  `instruction` varchar(100) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `numberOfIgredients` int(11) DEFAULT NULL,
  `ranking` float DEFAULT NULL,
  PRIMARY KEY (`recipeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `recipe` WRITE;
/*!40000 ALTER TABLE `recipe` DISABLE KEYS */;

INSERT INTO `recipe` (`recipeID`, `recipeName`, `instruction`, `time`, `numberOfIgredients`, `ranking`)
VALUES
	(1,'carrotcake ','connect to json',180,8,3.5);

/*!40000 ALTER TABLE `recipe` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table recipeConnection
# ------------------------------------------------------------

DROP TABLE IF EXISTS `recipeConnection`;

CREATE TABLE `recipeConnection` (
  `recipeID` int(11) unsigned NOT NULL,
  `foodName` varchar(30) NOT NULL DEFAULT '',
  `substitutable` tinyint(1) DEFAULT NULL,
  `essential` tinyint(1) DEFAULT NULL,
  `value` float DEFAULT NULL,
  PRIMARY KEY (`recipeID`,`foodName`),
  KEY `foodName` (`foodName`),
  CONSTRAINT `name FK` FOREIGN KEY (`foodName`) REFERENCES `ingredient` (`foodName`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `recipeConnection` WRITE;
/*!40000 ALTER TABLE `recipeConnection` DISABLE KEYS */;

INSERT INTO `recipeConnection` (`recipeID`, `foodName`, `substitutable`, `essential`, `value`)
VALUES
	(1,'carrot',0,1,10),
	(1,'egg',0,1,10),
	(1,'flour',1,0,7);

/*!40000 ALTER TABLE `recipeConnection` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table results
# ------------------------------------------------------------

DROP TABLE IF EXISTS `results`;

CREATE TABLE `results` (
  `recipeID` int(11) unsigned NOT NULL,
  `rankingPoints` float DEFAULT NULL,
  `ranking` float DEFAULT NULL,
  PRIMARY KEY (`recipieID`),
  CONSTRAINT `RecipeIDFK` FOREIGN KEY (`recipeID`) REFERENCES `recipe` (`recipeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `results` WRITE;
/*!40000 ALTER TABLE `results` DISABLE KEYS */;

INSERT INTO `results` (`recipieID`, `rankingPoints`, `ranking`)
VALUES
	(1,27,3.5);

/*!40000 ALTER TABLE `results` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
