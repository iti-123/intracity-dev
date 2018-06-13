<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/15/17
 * Time: 12:52 AM
 */

namespace Api\Services;


interface ISearchService
{

    /**
     * Adds a document to the Search store
     * @param $doc
     * @return mixed
     */
    public function add($doc);


    /**
     * Removes a document from the search store
     * @param $docId
     * @return mixed
     */
    public function remove($docId);


    /**
     * Searches for a document
     * @param $q
     * @param $fq
     * @param $facets
     * @param int $start
     * @param int $rows
     * @return mixed
     */
    public function search($q, $fq, $facets, $start = 0, $rows = 100);


    public function deltaImport($entity);
}