<?php

class chargerPointData {
    private $id, $name, $description, $pricePerKW, $connectorType, $imageUrl, $availableStatusId, $locationId;

    public function __construct($dbRow) {
        $this->id = $dbRow['charger_point_id'];
        $this->name = $dbRow['Name'];
        $this->description = $dbRow['charger_point_description'];
        $this->pricePerKW = $dbRow['price_per_kwatt'];
        $this->connectorType = $dbRow['connector_type'];
        $this->imageUrl = $dbRow['charger_image_url'];
        $this->availableStatusId = $dbRow['available_status_id'];
        $this->userId = $dbRow['user_id'];
        $this->locationId = $dbRow['location_id'];
    }

    // Getters
    public function getId()                { return $this->id; }
    public function getName()              { return $this->name; }
    public function getDescription()       { return $this->description; }
    public function getPricePerKW()        { return $this->pricePerKW; }
    public function getConnectorType()     { return $this->connectorType; }
    public function getImageUrl()          { return $this->imageUrl; }
    public function getAvailableStatusId() { return $this->availableStatusId; }
    public function getUserId()            { return $this->userId; }
    public function getLocationId()        { return $this->locationId; }

    // Setter for the image
    public function setImageUrl(string $imageUrl): void
    {
        // Optional: Sanitize input if you're assigning this manually
        $this->imageUrl = basename($imageUrl);
    }
}

