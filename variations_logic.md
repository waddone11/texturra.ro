How the Variation System Works
Models and Relationships
Product

A product can have many variations.
ProductVariation

Represents one specific combination of attributes for a product.
Its fillable fields include things like the product ID, SKU, price, and stock.
Relationship:
It has a many-to-many relationship with AttributeValue via a pivot table.
Attribute

This model defines an attribute (for example, “Unitate de măsură”, “Cantitate per ambalaj”, or “Image”).
Relationship:
An attribute has many possible AttributeValues.
AttributeValue

This model holds the possible values for a given attribute (e.g. “buc”, “cutie”, “bax” for packaging; “ml”, “litri”, etc. for measurement).
Relationship:
It belongs to an Attribute.
It has a many-to-many relationship with ProductVariation.
Pivot Table (product_variation_attribute_values)

This table connects ProductVariation and AttributeValue.
It stores which attribute values belong to which variation.
When you call $variation->attributeValues, Laravel automatically retrieves the related attribute values from this pivot table.
How Data Is Saved and Retrieved
When a product is created or updated with variations, each variation is stored as a separate record in the ProductVariation table.
The chosen attribute values for that variation are saved via the pivot table. For example, if a variation is defined by a “Unitate de măsură” of “buc” and a “Cantitate per ambalaj” of “cutie”, these two attribute value IDs are attached to that variation.
When you retrieve a product’s variations (for example, in the editProduct method), you load the variations along with their related attribute values. Then you can use the attribute name (from the Attribute model) to find which value is associated (from AttributeValue) and pass that into your UI.
