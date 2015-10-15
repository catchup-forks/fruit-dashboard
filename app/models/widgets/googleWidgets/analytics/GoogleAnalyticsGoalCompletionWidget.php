<?php
class GoogleAnalyticsGoalCompletionWidget extends HistogramWidget implements iServiceWidget
{
    use GoogleAnalyticsGoalWidgetTrait;

    /* Choices functions */
    public function goal($profileId=null) {
        if ($profileId) {
            /* Have specific profile. */
            $profile = $this->user()->googleAnalyticsProfiles()
                ->where('profile_id' , $profileId)
                ->first();
        } else if ($this->getSettings()['profile']) {
            /* Have specific profile. */
            $profile = $this->user()->googleAnalyticsProfiles()
                ->where('profile_id' , $this->getSettings()['profile'])
                ->first();
        } else {
            /* On init using first profile. */
            $profile = $this->user()->googleAnalyticsProfiles()
                ->first();
        }

        if (is_null($profile)) {
            throw new Exception("The selected profile is invalid.", 1);
        }

        foreach ($profile->goals as $goal) {
            $goals[$goal->goal_id] = $goal->name;
        }

        if (empty($goals)) {
            throw new Exception("No goal found.", 1);
        }

        return $goals;
    }
}
?>