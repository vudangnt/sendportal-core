<x-sendportal.text-field name="name" :label="__('Location Name')" :value="$location['name'] ?? null" />
<x-sendportal.select-field name="type" :label="__('Location Type')" :options="$types" :value="$location->type" />
