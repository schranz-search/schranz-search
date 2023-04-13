Index Operations
================

In the :doc:`../getting_started/index` documentation we already saw how we can add documents to our
created Index.

The following shows the basic usage as already shown in the "Getting Started" documentation. Under the
``$this->engine`` variable we assume that you have already injected your created ``Engine`` instance.

Save document
-------------

To add a document to an index we can use the ``saveDocument`` method. The only required field
for the document is the defined ``IdentifierField`` all others are optional and don't need to
be provided or can be null.

.. code-block:: php

    <?php

    $this->engine->saveDocument('blog', [
        'id' => '1',
        'title' => 'My first blog post',
        'description' => 'This is the description of my first blog post',
        'tags' => ['UI', 'UX'],
    ]);

Delete document
---------------

To remove a document from an index we can use the ``deleteDocument`` method. It only requires
the name of the index and the identifier of the document which should be removed.

.. code-block:: php

    <?php

    $this->engine->deleteDocument('blog', '1');

Bulk operations
---------------

Currently no bulk operations are implemented. Add your opinion to the
`Bulk issue <https://github.com/schranz-search/schranz-search/issues/24>`_
on Github.

Reindex operations
------------------

Currently no reindex operations are implemented. Add your opinion to the
`Reindex issue <https://github.com/schranz-search/schranz-search/issues/16>`_
on Github.

Next Steps
----------

After this short introduction about indexing we are able to add and remove our documents from the defined indexes.

In the next chapter, we examine the different conditions of :doc:`../search_and_filters/index` the abstraction provides.
